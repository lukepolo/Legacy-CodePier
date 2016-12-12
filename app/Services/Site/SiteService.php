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
use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;
use App\Contracts\Repository\RepositoryServiceContract as RepositoryService;

class SiteService implements SiteServiceContract
{
    protected $serverService;
    protected $remoteTaskService;
    protected $repositoryService;

    public $deploymentServices = [
        'php' => PHP::class,
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
     *
     * @return bool
     */
    public function create(Server $server, Site $site)
    {
        $this->getWebServerService($server)->createWebServerConfig($site);

        $this->remoteTaskService->ssh($server, 'codepier');

        $this->remoteTaskService->makeDirectory('/home/codepier/'.$site->domain);

        $this->serverService->restartWebServices($server);

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param \App\Models\Server\Server $server
     * @param \App\Models\Site\Site $site
     *
     * @return array
     */
    public function updateWebServerConfig(Server $server, Site $site)
    {
        $this->getWebServerService($server)->updateWebServerConfig($site);

        $this->serverService->restartWebServices($server);

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param \App\Models\Server\Server $server
     * @param Site $site
     * @param $newDomain
     * @param $oldDomain
     * @return array
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

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param Server $server
     * @param Site $site
     * @return array
     */
    private function remove(Server $server, Site $site)
    {
        $this->remoteTaskService->removeDirectory('/home/codepier/'.$site->domain);

        $this->getWebServerService($server)->removeWebServerConfig($site);

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param \App\Models\Server\Server $server
     * @param Site $site
     * @param SiteServerDeployment $siteServerDeployment
     * @param null $sha
     *
     * @throws DeploymentFailed
     * @internal param SiteDeployment $siteDeployment
     */
    public function deploy(Server $server, Site $site, SiteServerDeployment $siteServerDeployment, $sha = null)
    {
        $deploymentService = $this->getDeploymentService($server, $site);

        $this->repositoryService->importSshKeyIfPrivate($site);

        if (empty($lastCommit = $sha)) {
            $lastCommit = $this->repositoryService->getLatestCommit($site->userRepositoryProvider, $site->repository,
                $site->branch);
        }

        $siteServerDeployment->siteDeployment->git_commit = $lastCommit;
        $siteServerDeployment->siteDeployment->save();

        foreach ($siteServerDeployment->events as $event) {
            try {
                $start = microtime(true);

                event(new DeploymentStepStarted($site, $server, $event, $event->step));

                if(!empty($event->step->script)) {

                    $script = preg_replace("/[\n\r]/"," && ", $event->step->script);

                    $deploymentStepResult = $deploymentService->customStep($script);
                } else {
                    $internalFunction = $event->step->internal_deployment_function;
                    $deploymentStepResult = $deploymentService->$internalFunction($sha);
                }


                event(new DeploymentStepCompleted($site, $server, $event, $event->step, $deploymentStepResult, microtime(true) - $start));
            } catch (FailedCommand $e) {
                event(new DeploymentStepFailed($site, $server, $event, $event->step, [$e->getMessage()]));
                throw new DeploymentFailed($e->getMessage());
            }
        }

        event(new DeploymentCompleted($site, $server, $siteServerDeployment));
    }

    /**
     * @param \App\Models\Server\Server $server
     * @param \App\Models\Site\Site $site
     *
     * @return mixed
     */
    private function getDeploymentService(Server $server, Site $site)
    {
        $deploymentService = 'php';

        return new $this->deploymentServices[$deploymentService]($this->remoteTaskService, $server, $site);
    }

    /**
     * @param \App\Models\Server\Server $server
     * @param \App\Models\Site\Site $site
     *
     * @return array|bool
     */
    public function deleteSite(Server $server, Site $site)
    {
        $errors = $this->remoteTaskService->getErrors();

        if (empty($errors)) {
            $errors = $this->remove($server, $site);
        }

        if (empty($errors)) {
            $site->delete();

            return true;
        }

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param Site $site
     */
    public function createDeployHook(Site $site)
    {
        return $this->repositoryService->createDeployHook($site);
    }

    /**
     * @param Site $site
     */
    public function deleteDeployHook(Site $site)
    {
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
