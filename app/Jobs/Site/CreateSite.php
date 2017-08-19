<?php

namespace App\Jobs\Site;

use App\Models\Site\Site;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Traits\ModelCommandTrait;
use App\Jobs\Server\InstallPublicKey;
use Illuminate\Queue\SerializesModels;
use App\Services\Systems\SystemService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\Site\FixSiteServerConfigurations;
use App\Events\Server\UpdateServerConfigurations;
use App\Contracts\Site\SiteServiceContract as SiteService;

class CreateSite implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ModelCommandTrait;

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
        $this->command = $this->makeCommand($this->site, $this->server, 'Setting up Server '.$server->name.' for '.$site->name);
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Site\SiteService | SiteService $siteService
     */
    public function handle(SiteService $siteService)
    {
        $serverType = $this->server->type;

        if (
            $serverType === SystemService::WEB_SERVER ||
            $serverType === SystemService::WORKER_SERVER ||
            $serverType === SystemService::LOAD_BALANCER ||
            $serverType === SystemService::FULL_STACK_SERVER
        ) {
            rollback_dispatch(new InstallPublicKey($this->server, $this->site));
        }

        if (
            $serverType === SystemService::WEB_SERVER ||
            $serverType === SystemService::LOAD_BALANCER ||
            $serverType === SystemService::FULL_STACK_SERVER
        ) {
            $siteService->create($this->server, $this->site);
        }

        event(new UpdateServerConfigurations($this->server, $this->site, $this->command));
        event(new FixSiteServerConfigurations($this->site, $this->server));
    }
}
