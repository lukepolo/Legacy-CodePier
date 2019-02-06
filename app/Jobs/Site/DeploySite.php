<?php

namespace App\Jobs\Site;

use App\Models\Site\Site;
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
     */
    public function handle()
    {
        $site = $this->site->load(['deployments' => function($query) {
            $query->where('status', SiteDeployment::RUNNING)
                ->orWhere('status', SiteDeployment::QUEUED_FOR_DEPLOYMENT);
        }]);
        switch($site->deployments->count()) {
            case 0 :
                $this->setupDeployment();
                foreach ($this->siteDeployments as $siteServerDeployment) {
                    dispatch(
                        (new Deploy($this->site, $siteServerDeployment->server, $siteServerDeployment, $this->oldSiteDeployment))
                            ->onQueue(config('queue.channels.site_deployments'))
                    );
                }
                break;
            case 1 :
                $this->setupDeployment();
                dispatch(
                    (new self($this->site, null))
                        ->delay(30)
                        ->onQueue(config('queue.channels.site_deployments'))
                );
                break;
        }
    }
}
