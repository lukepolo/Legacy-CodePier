<?php

namespace App\Jobs\Site;

use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;
use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Models\Server\Server;
use App\Models\Site\Site;
use App\Services\Systems\SystemService;
use App\Traits\ServerCommandTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateSite implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $site;
    private $server;

    public $tries = 1;
    public $timeout = 60;

    /**
     * Create a new job instance.
     *
     * @param Server $server
     * @param Site   $site
     */
    public function __construct(Server $server, Site $site)
    {
        $this->site = $site;
        $this->server = $server;
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Site\SiteService | SiteService        $siteService
     * @param \App\Services\RemoteTaskService | RemoteTaskService $remoteTaskService
     *
     * @throws \App\Exceptions\FailedCommand
     * @throws \App\Exceptions\SshConnectionFailed
     * @throws \Exception
     */
    public function handle(SiteService $siteService, RemoteTaskService $remoteTaskService)
    {
        $serverType = $this->server->type;

        if (
            SystemService::WEB_SERVER === $serverType ||
            SystemService::LOAD_BALANCER === $serverType ||
            SystemService::FULL_STACK_SERVER === $serverType
        ) {
            $siteService->create($this->server, $this->site);
        }

        dispatch(
            (new FixSiteServerConfigurations($this->site))
                ->onQueue(config('queue.channels.server_commands'))
        );

        if (
            SystemService::WEB_SERVER === $serverType ||
            SystemService::WORKER_SERVER === $serverType ||
            SystemService::FULL_STACK_SERVER === $serverType
        ) {
            $this->runOnServer(function () use ($remoteTaskService) {
                $remoteTaskService->saveSshKeyToServer($this->site, $this->server);
            });
        }
    }
}
