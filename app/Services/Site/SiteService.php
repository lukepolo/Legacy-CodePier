<?php

namespace App\Services\Site;

use App\Models\Site\Site;
use App\Models\Server\Server;
use App\Exceptions\FailedCommand;
use App\Models\Site\SiteDeployment;
use App\Exceptions\DeploymentFailed;
use App\Services\Systems\SystemService;
use App\Events\Site\DeploymentCompleted;
use App\Events\Site\DeploymentStepFailed;
use App\Models\Site\SiteServerDeployment;
use App\Events\Site\DeploymentStepStarted;
use App\Contracts\Site\SiteServiceContract;
use App\Events\Site\DeploymentStepCompleted;
use App\Services\DeploymentServices\PHP\PHP;
use App\Services\DeploymentServices\Ruby\Ruby;
use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;
use App\Contracts\Repository\RepositoryServiceContract as RepositoryService;
use function response;

class SiteService implements SiteServiceContract
{
    protected $serverService;
    protected $remoteTaskService;
    protected $repositoryService;

    public $deploymentServices = [
        'php' => PHP::class,
        'ruby' => Ruby::class,
    ];

    /**
     * SiteService constructor.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @param \App\Services\RemoteTaskService | RemoteTaskService $remoteTaskService
     * @param \App\Services\Repository\RepositoryService | RepositoryService $repositoryService
     */
    public function __construct(
        ServerService $serverService,
        RemoteTaskService $remoteTaskService,
        RepositoryService $repositoryService
    ) {
        $this->serverService = $serverService;
        $this->remoteTaskService = $remoteTaskService;
        $this->repositoryService = $repositoryService;
    }

    /**
     * @param \App\Models\Server\Server $server
     * @param Site $site
     */
    public function create(Server $server, Site $site)
    {
        $this->getWebServerService($server)->createWebServerConfig($site);

        $this->remoteTaskService->ssh($server, 'codepier');

        $this->remoteTaskService->makeDirectory('/home/codepier/'.$site->domain);

        $this->serverService->restartWebServices($server);
    }

    /**
     * @param \App\Models\Server\Server $server
     * @param \App\Models\Site\Site $site
     */
    public function updateWebServerConfig(Server $server, Site $site)
    {
        $this->getWebServerService($server)->updateWebServerConfig($site);

        $this->serverService->restartWebServices($server);
    }

    /**
     * @param \App\Models\Server\Server $server
     * @param Site $site
     * @param $newDomain
     * @param $oldDomain
     */
    public function renameDomain(Server $server, Site $site, $newDomain, $oldDomain)
    {
        $this->remoteTaskService->ssh($server);

        $site->domain = $oldDomain;

        $this->getWebServerService($server)->removeWebServerConfig($site);

        $site->domain = $newDomain;

        $this->remoteTaskService->run('mv /home/codepier/'.$oldDomain.' /home/codepier/'.$site->domain);

        $this->getWebServerService($server)->createWebServerConfig($site);

        $this->serverService->restartWebServices($server);
    }

    /**
     * @param Server $server
     * @param Site $site
     */
    private function remove(Server $server, Site $site)
    {
        $this->remoteTaskService->removeDirectory('/home/codepier/'.$site->domain);

        $this->getWebServerService($server)->removeWebServerConfig($site);

        $this->serverService->restartWebServices($server);
    }

    /**
     * @param \App\Models\Server\Server $server
     * @param Site $site
     * @param SiteServerDeployment $siteServerDeployment
     * @param SiteDeployment $oldSiteDeployment
     * @throws DeploymentFailed
     */
    public function deploy(Server $server, Site $site, SiteServerDeployment $siteServerDeployment, SiteDeployment $oldSiteDeployment = null)
    {
        if($site->userRepositoryProvider) {
            $this->repositoryService->importSshKey($site);
        }

        $deploymentService = $this->getDeploymentService($server, $site, $oldSiteDeployment);

        foreach ($siteServerDeployment->events as $event) {
            try {
                if (empty($event->step)) {
                    $event->delete();
                    continue;
                }

                $start = microtime(true);

                event(new DeploymentStepStarted($site, $server, $event, $event->step));

                if (! empty($event->step->script)) {
                    $script = preg_replace("/[\n\r]/", ' && ', $event->step->script);

                    $deploymentStepResult = $deploymentService->customStep($script);
                } else {
                    $internalFunction = $event->step->internal_deployment_function;
                    $deploymentStepResult = $deploymentService->$internalFunction();
                }

                event(new DeploymentStepCompleted($site, $server, $event, $event->step, collect($deploymentStepResult)->filter()->implode("\n"), microtime(true) - $start));
            } catch (FailedCommand $e) {
                $log = collect($e->getMessage())->filter()->implode("\n");

                event(new DeploymentStepFailed($site, $server, $event, $event->step, $log));
                throw new DeploymentFailed($e->getMessage());
            }
        }

        $releaseFolder = $deploymentService->releaseTime;
        $siteServerDeployment->siteDeployment->update([
            'folder_name' => $releaseFolder,
            'git_commit' =>  $this->remoteTaskService->run("git --git-dir $deploymentService->release/.git rev-parse HEAD"),
            'commit_message' =>  trim($this->remoteTaskService->run("cd $deploymentService->release; git log -1 | sed -e '1,/Date/d'")),
        ]);

        event(new DeploymentCompleted($site, $server, $siteServerDeployment));
    }

    /**
     * @param \App\Models\Server\Server $server
     * @param \App\Models\Site\Site $site
     * @param SiteDeployment $siteDeployment
     * @return mixed
     */
    private function getDeploymentService(Server $server, Site $site, SiteDeployment $siteDeployment = null)
    {
        return new $this->deploymentServices[strtolower($site->type)]($this->remoteTaskService, $server, $site, $siteDeployment);
    }

    /**
     * @param \App\Models\Server\Server $server
     * @param \App\Models\Site\Site $site
     */
    public function deleteSite(Server $server, Site $site)
    {
        $this->remoteTaskService->ssh($server);

        $this->remove($server, $site);
    }

    /**
     * @param Site $site
     * @return Site|\Illuminate\Http\JsonResponse
     */
    public function createDeployHook(Site $site)
    {
        if(!$site->userRepositoryProvider) {
            return response()->json('The site does not have a connected repository', 400);
        }

        return $this->repositoryService->createDeployHook($site);
    }

    /**
     * @param Site $site
     * @return Site|\Illuminate\Http\JsonResponse
     */
    public function deleteDeployHook(Site $site)
    {
        if(!$site->userRepositoryProvider) {
            return response()->json('The site does not have a connected repository', 400);
        }

        return $this->repositoryService->deleteDeployHook($site);
    }

    /**
     * Gets the web server service.
     *
     * @param Server $server
     * @return mixed
     */
    private function getWebServerService(Server $server)
    {
        $webServices = $server->server_features[SystemService::WEB];

        if (isset($webServices['Nginx']['enabled'])) {
            return $this->serverService->getService(SystemService::WEB, $server);
        }
    }
}
