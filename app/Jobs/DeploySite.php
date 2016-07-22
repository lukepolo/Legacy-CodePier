<?php

namespace App\Jobs;

use App\Contracts\Server\Site\SiteServiceContract as SiteService;
use App\Events\Server\Site\DeploymentCompleted;
use App\Events\Server\Site\DeploymentFailed;
use App\Exceptions\FailedCommand;
use App\Models\Site;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeploySite extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public $site;
    public $server;

    /**
     * Create a new job instance.
     */
    public function __construct(Site $site)
    {
        $this->site = $site;
        $this->server = $site->server;
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\Site\SiteService | SiteService $siteService
     */
    public function handle(SiteService $siteService)
    {
        try {
            $siteService->deploy($this->server, $this->site);
            event(new DeploymentCompleted($this->site));
        } catch(FailedCommand $e) {
            event(new DeploymentFailed($this->site, $e->getMessage()));
        }
    }
}
