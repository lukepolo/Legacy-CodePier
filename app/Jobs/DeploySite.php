<?php

namespace App\Jobs;

use App\Contracts\Server\Site\SiteServiceContract as SiteService;
use App\Models\Site;
use App\Models\SiteDeployment;
use App\Models\User;
use App\Notifications\NewSiteDeployment;
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

    public $sha;
    public $site;
    public $servers = [];
    public $siteDeployment;

    /**
     * Create a new job instance.
     * @param Site $site
     * @param null $sha
     */
    public function __construct(Site $site, $sha = null)
    {
        $this->sha = $sha;
        $this->site = $site;
        $this->servers = $site->servers;

        $this->siteDeployment = SiteDeployment::create([
            'site_id' => $site->id,
            'status' => 'queued for deployment'
        ]);

        $this->siteDeployment->createSteps();

        $site->notify(new NewSiteDeployment());

//        event(new NewSiteDeployment($site, $this->siteDeployment));

    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\Site\SiteService | SiteService $siteService
     */
    public function handle(SiteService $siteService)
    {
        try {
            foreach($this->servers as $server) {
                $siteService->deploy($server, $this->site, $this->siteDeployment, $this->sha);
            }
        } catch(\App\Exceptions\DeploymentFailed $e) {
            // TODO - notifications here
//            event(new DeploymentFailedEvent($this->site, $this->siteDeployment, $e->getMessage()));
        }
    }
}
