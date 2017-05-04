<?php

namespace App\Jobs\Site;

use App\Models\Site\Site;
use Illuminate\Bus\Queueable;
use App\Models\Site\SiteDeployment;
use App\Exceptions\DeploymentFailed;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Site\SiteServerDeployment;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Site\NewSiteDeployment;

class DeploySite implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $site;
    public $servers = [];
    public $siteDeployment;
    public $oldSiteDeployment;

    public $tries = 1;
    public $timeout = 60;

    /**
     * Create a new job instance.
     *
     * @param Site $site
     * @param SiteDeployment $oldSiteDeployment
     */
    public function __construct(Site $site, SiteDeployment $oldSiteDeployment = null)
    {
        $this->site = $site;
        $this->servers = $site->provisionedServers;
        $this->oldSiteDeployment = $oldSiteDeployment;

        $this->siteDeployment = SiteDeployment::create([
            'site_id' => $site->id,
            'status'  => SiteDeployment::QUEUED_FOR_DEPLOYMENT,
        ]);

        foreach ($this->servers as $server) {
            SiteServerDeployment::create([
                'server_id' => $server->id,
                'status' => SiteDeployment::QUEUED_FOR_DEPLOYMENT,
                'site_deployment_id' => $this->siteDeployment->id,
            ])->createSteps();
        }

        $site->notify(new NewSiteDeployment($this->siteDeployment));

        $this->oldSiteDeployment = $oldSiteDeployment;
    }

    /**
     * Execute the job.
     *
     * @throws DeploymentFailed
     */
    public function handle()
    {
        foreach ($this->siteDeployment->serverDeployments as $serverDeployment) {
            dispatch(new Deploy($this->site, $serverDeployment, $this->oldSiteDeployment));
        }
    }
}
