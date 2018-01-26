<?php

namespace App\Jobs\Site;

use App\Models\Site\Site;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Exceptions\SshConnectionFailed;
use App\Services\Systems\SystemService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Jobs\Site\FixSiteServerConfigurations;
use App\Contracts\Site\SiteServiceContract as SiteService;

class DeleteSite implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $server;
    private $site;

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
        $this->server = $server;
        $this->site = $site;
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Site\SiteService | SiteService $siteService
     * @throws \App\Exceptions\FailedCommand
     * @throws \Exception
     */
    public function handle(SiteService $siteService)
    {
        $serverType = $this->server->type;

        if (
            $serverType === SystemService::WEB_SERVER ||
            $serverType === SystemService::LOAD_BALANCER ||
            $serverType === SystemService::FULL_STACK_SERVER
        ) {
            try {
                $siteService->deleteSite($this->server, $this->site);
            } catch (SshConnectionFailed $e) {
                // continue on
            }
        }

        dispatch(
            (new FixSiteServerConfigurations($this->site))
                ->onQueue(config('queue.channels.server_commands'))
        );
    }
}
