<?php

namespace App\Jobs\Site;

use App\Models\Site\Site;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Services\Systems\SystemService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Events\Site\FixSiteServerConfigurations;
use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;

class CreateSite implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $site;
    private $server;
    private $command;

    public $tries = 1;
    public $timeout = 60;

    /**
     * Create a new job instance.
     *
     * @param Server $server
     * @param Site $site
     */
    public function __construct(Server $server, Site $site)
    {
        $this->site = $site;
        $this->server = $server;
        $this->command = $this->makeCommand($this->server, $server, null, 'Setting up Server '.$server->name.' for '.$site->name);
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Site\SiteService | SiteService $siteService
     * @param \App\Services\RemoteTaskService | RemoteTaskService $remoteTaskService
     * @throws \App\Exceptions\FailedCommand
     * @throws \App\Exceptions\SshConnectionFailed
     * @throws \Exception
     */
    public function handle(SiteService $siteService, RemoteTaskService $remoteTaskService)
    {
        $start = microtime(true);

        $this->serverCommand->update([
            'started' => true,
        ]);

        $serverType = $this->server->type;

        if (
            $serverType === SystemService::WEB_SERVER ||
            $serverType === SystemService::LOAD_BALANCER ||
            $serverType === SystemService::FULL_STACK_SERVER
        ) {
            $siteService->create($this->server, $this->site);
        }

        event(new FixSiteServerConfigurations($this->site));

        if (
            $serverType === SystemService::WEB_SERVER ||
            $serverType === SystemService::WORKER_SERVER ||
            $serverType === SystemService::LOAD_BALANCER ||
            $serverType === SystemService::FULL_STACK_SERVER
        ) {
            $this->runOnServer(function () use ($remoteTaskService) {
                $remoteTaskService->saveSshKeyToServer($this->site, $this->server);
            });

            if ($this->wasSuccessful() && ! empty($this->site->repository)) {
                dispatch(new DeploySite($this->site));
            }
        } else {
            $this->updateServerCommand(microtime(true) - $start, ['Server Setup']);
        }
    }
}
