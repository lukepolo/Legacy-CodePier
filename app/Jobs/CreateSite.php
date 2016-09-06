<?php

namespace App\Jobs;

use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Models\Server;
use App\Models\Site;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class CreateSite.
 */
class CreateSite implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $site;
    protected $server;

    /**
     * Create a new job instance.
     *
     * @param Server $server
     * @param Site   $site
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
        $siteService->create($this->server, $this->site);
    }
}
