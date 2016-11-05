<?php

namespace App\Jobs\Site;

use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Models\Server\Server;
use App\Models\Site\Site;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
     */
    public function handle(SiteService $siteService)
    {
        //        $siteService->create($this->server, $this->site);
//
//        $this->site->cronJobs->each(function ($model) {
//            $model->fire('created');
//        });
//
//        $this->site->files->each(function ($model) {
//            $model->fire('created');
//        });
//
//        $this->site->firewallRules->each(function ($model) {
//            $model->fire('created');
//        });

        $this->site->sshKeys->each(function ($model) {
            $model->fire('created');
        });
//
//        $this->site->ssls->each(function ($model) {
//            $model->fire('created');
//        });
//
//        $this->site->workers->each(function ($model) {
//            $model->fire('created');
//        });
    }
}
