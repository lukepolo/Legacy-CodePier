<?php

namespace App\Services\Site;

use App\Models\Site\Site;
use App\Models\FirewallRule;
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
use App\Events\Site\SiteFirewallRuleCreated;
use App\Services\DeploymentServices\PHP\PHP;
use App\Services\DeploymentServices\Ruby\Ruby;
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
        $this->getWebServerService($server)->updateWebServerConfig($site, $server->type);

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
        if ($site->userRepositoryProvider) {
            $this->repositoryService->importSshKey($site);
        }

        $deploymentService = $this->getDeploymentService($server, $site, $oldSiteDeployment);

        foreach ($siteServerDeployment->events as $index => $event) {
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

                if ($index === 0) {
                    $releaseFolder = $deploymentService->releaseTime;
                    $siteServerDeployment->siteDeployment->update([
                        'folder_name' => $releaseFolder,
                        'git_commit' =>  $this->remoteTaskService->run("git --git-dir $deploymentService->release/.git rev-parse HEAD"),
                        'commit_message' =>  trim($this->remoteTaskService->run("cd $deploymentService->release; git log -1 | sed -e '1,/Date/d'")),
                    ]);
                }

                event(new DeploymentStepCompleted($site, $server, $event, $event->step, collect($deploymentStepResult)->filter()->implode("\n"), microtime(true) - $start));
            } catch (FailedCommand $e) {
                $log = collect($e->getMessage())->filter()->implode("\n");

                event(new DeploymentStepFailed($site, $server, $event, $event->step, $log));
                throw new DeploymentFailed($e->getMessage());
            }
        }

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
        if (! $site->userRepositoryProvider) {
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
        if (! $site->userRepositoryProvider) {
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

    /**
     * Creates a firewall rule for a site if it does not exist.
     *
     * @param Site $site
     * @param $port
     * @param $type
     * @param $description
     * @param null $fromIp
     *
     * return FirewallRule | null
     */
    public function createFirewallRule(Site $site, $port, $type, $description, $fromIp = null)
    {
        $type = $port === '*' ? 'both' : $type;

        if (! $site->firewallRules
            ->where('port', $port)
            ->where('from_ip', $fromIp)
            ->where('type', $type)
            ->count()
        ) {
            $firewallRule = FirewallRule::create([
                'port' => $port,
                'type' => $type,
                'from_ip' => $fromIp,
                'description' => $description,
            ]);

            $site->firewallRules()->save($firewallRule);

            event(new SiteFirewallRuleCreated($site, $firewallRule));

            return $firewallRule;
        }
    }
}
