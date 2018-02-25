<?php

namespace App\Jobs\Site;

use App\Models\Command;
use App\Models\Site\Site;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Contracts\Site\SiteServiceContract as SiteService;

class UpdateWebConfig implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $site;
    private $server;

    public $tries = 1;
    public $timeout = 60;

    /**
     * Create a new job instance.
     *
     * @param Server  $server
     * @param Site    $site
     * @param Command $siteCommand
     */
    public function __construct(Server $server, Site $site, Command $siteCommand)
    {
        $this->site = $site;
        $this->server = $server;

        $this->makeCommand($server, $site, $siteCommand);
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Site\SiteService | SiteService $siteService
     *
     * @throws \Exception
     */
    public function handle(SiteService $siteService)
    {
        $this->runOnServer(function () use ($siteService) {
            $siteService->updateWebServerConfig($this->server, $this->site);
        });
    }
}
