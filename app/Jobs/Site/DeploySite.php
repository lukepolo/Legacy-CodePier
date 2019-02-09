<?php

namespace App\Jobs\Site;

use App\Models\Site\Site;
use App\Notifications\Site\SiteDeploymentFailed;
use App\Services\Site\SiteService;
use Illuminate\Bus\Queueable;
use App\Models\Site\SiteDeployment;
use Illuminate\Queue\SerializesModels;
use App\Services\Systems\SystemService;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Site\SiteServerDeployment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\Site\NewSiteDeployment;

class DeploySite implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $site;
    public $servers = [];
    public $siteDeployment;
    public $oldSiteDeployment;
    public $siteDeployments = [];

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
        $this->oldSiteDeployment = $oldSiteDeployment;
    }

    public function setupDeployment() {
        $this->servers = $this->site->provisionedServers;

        $availableServers = $this->site->filterServersByType([
            SystemService::WEB_SERVER,
            SystemService::WORKER_SERVER,
            SystemService::FULL_STACK_SERVER,
        ]);

        if (! empty($availableServers)) {
            $this->siteDeployment = SiteDeployment::create([
                'site_id' => $this->site->id,
                'status'  => SiteDeployment::QUEUED_FOR_DEPLOYMENT,
            ]);

            foreach ($availableServers as $server) {
                $this->siteDeployments[] = SiteServerDeployment::create([
                    'server_id' => $server->id,
                    'status' => SiteDeployment::QUEUED_FOR_DEPLOYMENT,
                    'site_deployment_id' => $this->siteDeployment->id,
                ])->createSteps();
            }

            $this->site->notify(new NewSiteDeployment($this->siteDeployment));
        }
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Site\SiteService | SiteService $siteService
     */
    public function handle(SiteService $siteService)
    {
        $site = $this->site->load(['deployments' => function($query) {
            $query->where('status', SiteDeployment::RUNNING)
                ->orWhere('status', SiteDeployment::QUEUED_FOR_DEPLOYMENT);
        }]);

        foreach($site->deployments as $deployment) {
            foreach($deployment->serverDeployments as $serverDeployment) {
                if($serverDeployment->job_id) {
                    posix_kill($serverDeployment->job_id, SIGKILL);
                }
                $siteService->deployFailed($this->site, $serverDeployment->server, $serverDeployment, "Site triggered new deployment", microtime(true));
            }
        }

        $this->setupDeployment();
        foreach ($this->siteDeployments as $siteServerDeployment) {
            dispatch(
                (new Deploy($this->site, $siteServerDeployment->server, $siteServerDeployment, $this->oldSiteDeployment))
                    ->onQueue(config('queue.channels.site_deployments'))
            );
        }
    }
}
