<?php

namespace App\Jobs\Server;

use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Exceptions\DeploymentFailed;
use App\Models\Site\Site;
use App\Models\Site\SiteDeployment;
use App\Notifications\NewSiteDeployment;
use App\Notifications\SiteDeploymentFailed;
use App\Notifications\SiteDeploymentSuccessful;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class DeploySite.
 */
class DeploySite implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $sha;
    public $site;
    public $servers = [];
    public $siteDeployment;

    /**
     * Create a new job instance.
     *
     * @param Site $site
     * @param null $sha
     */
    public function __construct(Site $site, $sha = null)
    {
        $this->sha = $sha;
        $this->site = $site;
        $this->servers = $site->provisionedServers;

        $this->siteDeployment = SiteDeployment::create([
            'site_id' => $site->id,
            'status'  => 'queued for deployment',
        ]);

        $this->siteDeployment->createSteps();

        $site->notify(new NewSiteDeployment($site, $this->siteDeployment));
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Site\SiteService | SiteService $siteService
     */
    public function handle(SiteService $siteService)
    {
        try {
            foreach ($this->servers as $server) {
                $siteService->deploy($server, $this->site, $this->siteDeployment, $this->sha);
                $this->site->notify(new SiteDeploymentSuccessful($this->site, $this->siteDeployment));
            }
        } catch (DeploymentFailed $e) {
            $this->site->notify(new SiteDeploymentFailed($this->site, $this->siteDeployment, $e->getMessage()));
        }
    }
}
