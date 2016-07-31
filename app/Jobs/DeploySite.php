<?php

namespace App\Jobs;

use App\Contracts\Server\Site\SiteServiceContract as SiteService;
use App\Events\Server\Site\DeploymentFailed;
use App\Events\Server\Site\NewSiteDeployment;
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

    public $sha;
    public $site;
    public $server;
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
        $this->server = $site->server;


        $this->siteDeployment = SiteDeployment::create([
            'site_id' => $site->id,
            'status' => 'queued for deployment'
        ]);

        $this->siteDeployment->createSteps();

//        event(new NewSiteDeployment($site, $this->siteDeployment));

    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\Site\SiteService | SiteService $siteService
     */
    public function handle(SiteService $siteService)
    {
        dump('test 2');
        try {
            $siteService->deploy($this->server, $this->site, $this->siteDeployment, $this->sha);
        } catch(\App\Exceptions\DeploymentFailed $e) {
            event(new DeploymentFailed($this->site, $this->siteDeployment, $e->getMessage()));
        }
    }
}
