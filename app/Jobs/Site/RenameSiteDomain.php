<?php

namespace App\Jobs\Site;

use App\Models\Site\Site;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Site\SiteServiceContract as SiteService;

class RenameSiteDomain implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $site;
    private $newDomain;
    private $oldDomain;

    /**
     * Create a new job instance.
     *
     * @param Site $site
     * @param $newDomain
     * @param $oldDomain
     */
    public function __construct(Site $site, $newDomain, $oldDomain)
    {
        $this->site = $site;
        $this->oldDomain = $oldDomain;
        $this->newDomain = $newDomain;
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Site\SiteService | SiteService $siteService
     */
    public function handle(SiteService $siteService)
    {
        foreach ($this->site->provisionedServers as $server) {
            $siteService->renameDomain($server, $this->site, $this->newDomain, $this->oldDomain);
        }
    }
}
