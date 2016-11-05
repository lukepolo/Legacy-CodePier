<?php

namespace App\Services\Site;

use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;
use App\Contracts\Repository\RepositoryServiceContract as RepositoryService;
use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Contracts\Site\SiteServiceContract;
use App\Events\Site\DeploymentStepCompleted;
use App\Events\Site\DeploymentStepFailed;
use App\Events\Site\DeploymentStepStarted;
use App\Exceptions\DeploymentFailed;
use App\Exceptions\FailedCommand;
use App\Models\Server\Server;
use App\Models\Site\Site;
use App\Models\Site\SiteDeployment;
use App\Services\DeploymentServices\PHP;
use App\Services\Systems\SystemService;

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
     * @param $domain
     *
     * @return array
     */
    public function renameDomain(Server $server, Site $site, $domain)
    {
        // TODO
        dd('Needs to be tested');

        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->run('mv /home/codepier/'.$site->domain.' /home/codepier/'.$domain);

        $this->remove($server, $site);

        $this->create($server, $site);

        $site->domain = $domain;

        $site->save();

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
        $this->getWebServerService($server)->removeWebServerConfig($site);

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param \App\Models\Server\Server $server
     * @param Site $site
     * @param \App\Models\Site\SiteDeployment $siteDeployment
     * @param null $sha
     *
     * @throws DeploymentFailed
     */
    public function deploy(Server $server, Site $site, SiteDeployment $siteDeployment, $sha = null)
    {
        $deploymentService = $this->getDeploymentService($server, $site);

        if (empty($lastCommit = $sha)) {
            $lastCommit = $this->repositoryService->getLatestCommit($site->userRepositoryProvider, $site->repository,
                $site->branch);
        }

        $siteDeployment->git_commit = $lastCommit;
        $siteDeployment->save();

        foreach ($siteDeployment->events as $event) {
            try {
                $start = microtime(true);

                event(new DeploymentStepStarted($site, $event, $event->step));

                $internalFunction = $event->step->internal_deployment_function;

                $log = $deploymentService->$internalFunction($sha);

                event(new DeploymentStepCompleted($site, $event, $event->step, $log, microtime(true) - $start));
            } catch (FailedCommand $e) {
                event(new DeploymentStepFailed($site, $event, $e->getMessage()));
                throw new DeploymentFailed($e->getMessage());
            }
        }

        $this->remoteTaskService->ssh($server);
        $this->serverService->restartWebServices($server);

        // TODO - should be a notification
//        event(new DeploymentCompleted($site, $siteDeployment, 'Commit #####', $this->remoteTaskService->getOutput()));
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

        if(isset($webServices['Nginx']['enabled'])) {
            return $this->serverService->getService(SystemService::WEB ,$server);
        }
    }

}
