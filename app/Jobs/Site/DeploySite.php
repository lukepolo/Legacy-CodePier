<?php

namespace App\Jobs\Site;

use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Exceptions\DeploymentFailed;
use App\Models\Site\Site;
use App\Models\Site\SiteDeployment;
use App\Notifications\Site\NewSiteDeployment;
use App\Notifications\Site\SiteDeploymentFailed;
use App\Notifications\Site\SiteDeploymentSuccessful;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeploySite implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $sha;
    public $site;
    public $servers = [];
    public $siteDeployments = [];

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

        foreach ($this->servers as $server) {
            $this->siteDeployments[$server->id] = SiteDeployment::create([
                'site_id' => $site->id,
                'server_id' => $server->id,
                'status'  => 'queued for deployment',
            ])->createSteps();

            $site->notify(new NewSiteDeployment($site, last($this->siteDeployments)));
        }
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Site\SiteService | SiteService $siteService
     */
    public function handle(SiteService $siteService)
    {
        $success = true;
        foreach ($this->servers as $server) {
            $siteDeployment = $this->siteDeployments[$server->id];
            try {
                $siteService->deploy($server, $this->site, $siteDeployment, $this->sha);
            } catch (DeploymentFailed $e) {
                $success = false;
                $this->site->notify(new SiteDeploymentFailed($this->site, $siteDeployment, $e->getMessage()));
            }
        }

        if ($success) {
            $this->site->notify(new SiteDeploymentSuccessful($this->site));
        }
    }
}
