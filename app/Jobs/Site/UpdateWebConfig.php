<?php

namespace App\Jobs\Site;

use App\Models\Site\Site;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Exceptions\ServerCommandFailed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Site\SiteServiceContract as SiteService;

class UpdateWebConfig implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

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
        $this->site = $site;
        $this->server = $server;
        $this->makeCommand($server, $site, null, 'Updating Web Config');
    }

    /**
     * Execute the job.
     * @param \App\Services\Site\SiteService | SiteService $siteService
     * @throws ServerCommandFailed
     */
    public function handle(SiteService $siteService)
    {
        $this->runOnServer(function () use ($siteService) {
            $siteService->updateWebServerConfig($this->server, $this->site);
        });

        if (! $this->wasSuccessful()) {
            throw new ServerCommandFailed($this->getCommandErrors());
        }
    }
}
