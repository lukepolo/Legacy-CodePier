<?php

namespace App\Jobs\Site;

use App\Models\Site\Site;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;

class CreateSite implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $server;
    private $site;

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
     * @param \App\Services\RemoteTaskService | RemoteTaskService $remoteTaskService
     */
    public function handle(SiteService $siteService, RemoteTaskService $remoteTaskService)
    {
        $siteService->create($this->server, $this->site);

        $this->site->cronJobs->each(function ($model) {
        });

        $this->site->files->each(function ($model) {
        });

        $this->site->firewallRules->each(function ($model) {
        });

        $this->site->sshKeys->each(function ($model) {
        });

        $this->site->ssls->each(function ($model) {
        });

        $this->site->workers->each(function ($model) {
        });

        $remoteTaskService->saveSshKeyToServer($this->site, $this->server);
    }
}
