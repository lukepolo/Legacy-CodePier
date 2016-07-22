<?php

namespace App\Jobs;

use App\Contracts\Server\Site\SiteServiceContract as SiteService;
use App\Events\Server\Site\DeploymentCompleted;
use App\Events\Server\Site\DeploymentFailed;
use App\Events\Server\Site\NewSiteDeployment;
use App\Exceptions\FailedCommand;
use App\Models\Site;
use App\Models\SiteDeployment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class DeploySite
 * @package App\Jobs
 */
class DeploySite extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public $site;
    public $server;
    public $siteDeployment;

    /**
     * Create a new job instance.
     */
    public function __construct(Site $site)
    {
        $this->site = $site;
        $this->server = $site->server;

        $this->siteDeployment = SiteDeployment::create([
            'site_id' => $site->id
        ]);

        $this->siteDeployment->createSteps();

        event(new NewSiteDeployment($site, $this->siteDeployment));
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\Site\SiteService | SiteService $siteService
     */
    public function handle(SiteService $siteService)
    {
        try {
            $siteService->deploy($this->server, $this->site, $this->siteDeployment);
            event(new DeploymentCompleted($this->site, $this->siteDeployment));
        } catch(FailedCommand $e) {
            event(new DeploymentFailed($this->site, $this->siteDeployment, $e->getMessage()));
        }
    }
}
