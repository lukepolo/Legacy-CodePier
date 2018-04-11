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
use App\Services\DeploymentServices\HTML\HTML;
use App\Services\DeploymentServices\Ruby\Ruby;
use App\Models\Site\Deployment\DeploymentEvent;
use App\Notifications\Site\SiteDeploymentFailed;
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
        'html' => HTML::class,
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
     * @throws FailedCommand
     * @throws \App\Exceptions\SshConnectionFailed
     * @throws \Exception
     */
    public function create(Server $server, Site $site)
    {
        $webService = $this->getWebServerService($server);

        if (! empty($webService)) {
            $webService->createWebServerConfig($site);
        }

        $this->remoteTaskService->ssh($server, 'codepier');

        $this->remoteTaskService->makeDirectory('/home/codepier/'.$site->domain);

        $location = '/home/codepier/'.$site->domain.($site->zero_downtime_deployment ? '/current' : null).'/'.$site->web_directory;

        $this->remoteTaskService->makeDirectory($location);
        $this->remoteTaskService->run("ln -sf /opt/codepier/landing/index.html $location/index.html");

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
     * @throws FailedCommand
     * @throws \App\Exceptions\SshConnectionFailed
     * @throws \Exception
     */
    public function renameDomain(Server $server, Site $site, $newDomain, $oldDomain)
    {
        $this->remoteTaskService->ssh($server);

        $site->domain = $oldDomain;

        $webService = $this->getWebServerService($server);
        if (! empty($webService)) {
            $webService->removeWebServerConfig($site);
        }


        $site->domain = $newDomain;

        $this->remoteTaskService->run('mv /home/codepier/' . $oldDomain . ' /home/codepier/' . $site->domain);

        if (! empty($webService)) {
            $webService->createWebServerConfig($site);
        }

        $this->serverService->restartWebServices($server);
    }

    /**
     * @param Server $server
     * @param Site $site
     * @throws FailedCommand
     * @throws \App\Exceptions\SshConnectionFailed
     * @throws \Exception
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
     * @throws \App\Exceptions\SshConnectionFailed
     * @throws \Exception
     */
    public function deploy(Server $server, Site $site, SiteServerDeployment $siteServerDeployment, SiteDeployment $oldSiteDeployment = null)
    {
        $deploymentService = $this->getDeploymentService($server, $site, $oldSiteDeployment);

        $hasAfterDeploymentEvents = false;

        if ($deploymentService->rollback) {
            $deploymentService->rollback();
        } else {
            $events = $siteServerDeployment->events->where('completed', 0);

            foreach ($events as $index => $event) {
                try {
                    if (! empty($event->step)) {
                        $startTime = microtime(true);

                        if (! $siteServerDeployment->pending_complete && $event->step->after_deploy) {
                            $hasAfterDeploymentEvents = true;
                            continue;
                        }

                        $this->stepStarted($site, $server, $event);
                        $deploymentStepLog = $this->runDeploymentStep($deploymentService, $event);
                        $this->stepCompleted($site, $server, $event, $startTime, $deploymentStepLog);
                    }
                } catch (FailedCommand $e) {
                    $this->captureFailedStep($site, $server, $event, $startTime, $e->getMessage());
                }
            }
        }

        $siteServerDeployment->siteDeployment->update([
            'folder_name' => $deploymentService->releaseTime,
            'git_commit' =>  $this->remoteTaskService->run("git --git-dir $deploymentService->release/.git rev-parse HEAD"),
            'commit_message' =>  trim($this->remoteTaskService->run("cd $deploymentService->release; git log -1 | sed -e '1,/Date/d'")),
        ]);

        if (! $hasAfterDeploymentEvents) {
            broadcast(new DeploymentCompleted($site, $server, $siteServerDeployment));
        }

        $siteServerDeployment->update([
            'pending_complete' => true
        ]);
    }

    /**
     * @param Site $site
     * @param Server $server
     * @param SiteServerDeployment $serverDeployment
     * @param $message
     * @param $startTime
     */
    public function deployFailed(Site $site, Server $server, SiteServerDeployment $serverDeployment, $message, $startTime)
    {
        $event = $serverDeployment->events->first(function ($event) {
            return $event->completed == false || $event->failed;
        });

        if (! $event) {
            $event = $serverDeployment->events->last();
        }

        if (! $event->failed) {
            broadcast(new DeploymentStepFailed($site, $server, $event, $message, microtime(true) - $startTime));
        }

        $site->notify(new SiteDeploymentFailed($serverDeployment, $message));
    }

    /**
     * @param Server $server
     * @param Site $site
     * @param SiteServerDeployment $siteServerDeployment
     * @param SiteDeployment $siteDeployment
     * @throws DeploymentFailed
     */
    public function runStepsAfterDeploy(Server $server, Site $site, SiteServerDeployment $siteServerDeployment, SiteDeployment $siteDeployment)
    {
        $deploymentService = $this->getDeploymentService($server, $site, $siteDeployment);
        $events = $siteServerDeployment->events->where('completed', 0);

        foreach ($events as $index => $event) {
            try {
                if (! empty($event->step)) {
                    $startTime = microtime(true);
                    $this->stepStarted($site, $server, $event);
                    $deploymentStepLog = $this->runDeploymentStep($deploymentService, $event);
                    $this->stepCompleted($site, $server, $event, $startTime, $deploymentStepLog);
                }
            } catch (FailedCommand $e) {
                $this->captureFailedStep($site, $server, $event, $startTime, $e->getMessage());
            }
        }

        broadcast(new DeploymentCompleted($site, $server, $siteServerDeployment));
    }

    /**
     * @param Site $site
     * @param Server $server
     * @param DeploymentEvent $event
     * @param $startTime
     * @param string $message
     * @throws DeploymentFailed
     */
    private function captureFailedStep(Site $site, Server $server, DeploymentEvent $event, $startTime, string $message)
    {
        $log = collect($message)->filter()->implode("\n");
        broadcast(new DeploymentStepFailed($site, $server, $event, $log, microtime(true) - $startTime));
        throw new DeploymentFailed($message);
    }

    /**
     * @param $site
     * @param $server
     * @param $event
     */
    private function stepStarted($site, $server, $event)
    {
        broadcast(new DeploymentStepStarted($site, $server, $event));
    }

    /**
     * @param $site
     * @param $server
     * @param $event
     * @param $startTime
     * @param $log
     */
    private function stepCompleted(Site $site, Server $server, DeploymentEvent $event, $startTime, $log)
    {
        broadcast(new DeploymentStepCompleted($site, $server, $event, collect($log)->filter()->implode("\n"), microtime(true) - $startTime));
    }

    /**
     * @param $deploymentService
     * @param DeploymentEvent $event
     * @return mixed
     */
    private function runDeploymentStep($deploymentService, DeploymentEvent $event)
    {
        if (! empty($event->step->script)) {
            $script = preg_replace("/[\n\r]/", ' && ', $event->step->script);
            $deploymentStepLog = $deploymentService->customStep($script);
        } else {
            $internalFunction = $event->step->internal_deployment_function;
            $deploymentStepLog = $deploymentService->$internalFunction();
        }

        return $deploymentStepLog;
    }

    /**
     * @param \App\Models\Server\Server $server
     * @param \App\Models\Site\Site $site
     * @param SiteDeployment $siteDeployment
     * @return mixed
     */
    private function getDeploymentService(Server $server, Site $site, SiteDeployment $siteDeployment = null)
    {
        return new $this->deploymentServices[strtolower($site->type)]($this->remoteTaskService, $this->repositoryService, $server, $site, $siteDeployment);
    }

    /**
     * @param \App\Models\Server\Server $server
     * @param \App\Models\Site\Site $site
     * @throws FailedCommand
     * @throws \App\Exceptions\SshConnectionFailed
     * @throws \Exception
     */
    public function deleteSite(Server $server, Site $site)
    {
        $this->remoteTaskService->ssh($server);

        $this->remove($server, $site);
    }

    /**
     * @param Site $site
     * @return Site|\Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\SiteUserProviderNotConnected
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
     * @throws \App\Exceptions\SiteUserProviderNotConnected
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
     * @return FirewallRule | null
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

    /**
     * @param Site $site
     */
    public function resetWorkflow(Site $site)
    {
        if ($site->servers->count() > 1) {
            $site->load([
                'daemons',
                'cronJobs',
            ]);

            $workflow = [
                'message' => 'You have multiple servers and may have to update your settings. Use the server icon <span class="icon-server"></span> to specify which server you want items to run on.',
            ];

            $order = 0;

            if ($site->filterServersByType([
                SystemService::WEB_SERVER,
                SystemService::WORKER_SERVER,
                SystemService::FULL_STACK_SERVER,
            ])->count() > 0) {
                $workflow['site_deployment'] = [
                    'step' => 'site_deployment',
                    'order' => ++$order,
                    'completed' => false,
                ];

                if ($site->daemons->count()) {
                    $workflow['site_daemons'] = [
                        'step' => 'site_daemons',
                        'order' => ++$order,
                        'completed' => false,
                    ];
                }
            }

            if ($site->cronJobs->count()) {
                $workflow['site_cron_jobs'] = [
                    'step' => 'site_cron_jobs',
                    'order' => ++$order,
                    'completed' => false,
                ];
            }

            $site->update([
                'workflow' => $workflow,
            ]);
        } else {
            $site->update([
                'workflow' => [],
            ]);
        }
    }
}
