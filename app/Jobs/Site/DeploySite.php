<?php

namespace App\Jobs\Site;

use App\Models\Site\Site;
use Illuminate\Bus\Queueable;
use App\Models\Site\SiteDeployment;
use App\Exceptions\DeploymentFailed;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\Site\DeploymentStepFailed;
use App\Models\Site\SiteServerDeployment;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Site\NewSiteDeployment;
use App\Notifications\Site\SiteDeploymentFailed;
use App\Notifications\Site\SiteDeploymentSuccessful;
use App\Contracts\Site\SiteServiceContract as SiteService;

class DeploySite implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $site;
    public $servers = [];
    public $siteDeployment;
    public $oldSiteDeployment;

    public $tries = 1;
    public $timeout = 300;

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
     * @param \App\Services\Site\SiteService | SiteService $siteService
     * @throws DeploymentFailed
     */
    public function handle(SiteService $siteService)
    {
        $success = true;
        foreach ($this->siteDeployment->serverDeployments as $serverDeployment) {
            try {
                $siteService->deploy($serverDeployment->server, $this->site, $serverDeployment, $this->oldSiteDeployment);
            } catch (\Exception $e) {
                $message = $e->getMessage();

                if (get_class($e) == \Exception::class) {
                    app('sentry')->captureException($e);
                    $message = 'The error has been reported and we are looking into it.';
                }

                $success = false;

                $event = $serverDeployment->events->first(function ($event) {
                    return $event->completed == false || $event->failed;
                });

                if (! $event) {
                    $event = $serverDeployment->events->last();
                }

                if (! $event->failed) {
                    event(new DeploymentStepFailed($this->site, $serverDeployment->server, $event, $event->step, [$message]));
                }

                $this->site->notify(new SiteDeploymentFailed($serverDeployment, $message));
            }
        }

        if ($success) {
            $this->site->notify(new SiteDeploymentSuccessful($this->siteDeployment));
        }
    }
}
