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
use App\Contracts\Site\SiteServiceContract as SiteService;

class RenameSiteDomain implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $site;
    private $server;
    private $newDomain;
    private $oldDomain;

    public $tries = 1;
    public $timeout = 60;

    /**
     * Create a new job instance.
     *
     * @param Server $server
     * @param Site $site
     * @param $newDomain
     * @param $oldDomain
     * @param Command $siteCommand
     * @internal param Site $site
     */
    public function __construct(Server $server, Site $site, $newDomain, $oldDomain, Command $siteCommand)
    {
        $this->site = $site;
        $this->server = $server;
        $this->oldDomain = $oldDomain;
        $this->newDomain = $newDomain;

        $this->makeCommand($server, $site, $siteCommand);
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Site\SiteService | SiteService $siteService
     * @throws \Exception
     */
    public function handle(SiteService $siteService)
    {
        $this->runOnServer(function () use ($siteService) {
            $siteService->renameDomain($this->server, $this->site, $this->newDomain, $this->oldDomain);
        });
    }
}
