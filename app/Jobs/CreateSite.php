<?php

namespace App\Jobs;

use App\Contracts\Server\Site\SiteServiceContract as SiteService;
use App\Models\Server;
use App\Models\Site;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class CreateSite
 * @package App\Jobs
 */
class CreateSite extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, DispatchesJobs;

    protected $site;
    protected $server;

    /**
     * Create a new job instance.
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
     * @param \App\Services\Server\Site\SiteService | SiteService $siteService
     */
    public function handle(SiteService $siteService)
    {
        dd('dont do this yet!');
        $siteService->create($this->server, $this->site);
    }
}
