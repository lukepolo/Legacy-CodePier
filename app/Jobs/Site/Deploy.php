<?php

namespace App\Jobs\Site;

use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Events\Site\DeploymentStepFailed;
use App\Events\Site\DeploymentStepStarted;
use App\Models\Site\Site;
use App\Models\Site\SiteServerDeployment;
use App\Notifications\Site\SiteDeploymentFailed;
use App\Notifications\Site\SiteDeploymentSuccessful;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Deploy implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $site;
    public $server;
    public $siteDeployment;
    public $serverDeployment;
    public $oldSiteDeployment;

    public $tries = 1;
    public $timeout = 600;

    /**
     * Create a new job instance.
     *
     * @param SiteServerDeployment $serverDeployment
     * @param null                 $oldSiteDeployment
     */
    public function __construct(Site $site, SiteServerDeployment $serverDeployment, $oldSiteDeployment = null)
    {
        $this->site = $site;
        $this->serverDeployment = $serverDeployment;

        $this->serverDeployment->load([
            'events',
            'server',
            'siteDeployment.serverDeployments',
        ]);

        $this->oldSiteDeployment = $oldSiteDeployment;

        $this->server = $this->serverDeployment->server;
        $this->siteDeployment = $this->serverDeployment->siteDeployment;
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Site\SiteService | SiteService $siteService
     */
    public function handle(SiteService $siteService)
    {
        $success = true;

        try {
            $start = microtime(true);
            $firstEvent = $this->serverDeployment->events->first();
            broadcast(new DeploymentStepStarted($this->site, $this->server, $firstEvent, $firstEvent->step));
            $siteService->deploy($this->server, $this->site, $this->serverDeployment, $this->oldSiteDeployment);
        } catch (\Exception $e) {
            $message = $e->getMessage();

            if (\Exception::class == get_class($e)) {
                app('sentry')->captureException($e);
                $message = 'The error has been reported and we are looking into it.';
            }

            $success = false;

            $event = $this->serverDeployment->events->first(function ($event) {
                return false == $event->completed || $event->failed;
            });

            if (! $event) {
                $event = $this->serverDeployment->events->last();
            }

            if (! $event->failed) {
                broadcast(new DeploymentStepFailed($this->site, $this->server, $event, $event->step, $message, microtime(true) - $start));
            }

            $this->site->notify(new SiteDeploymentFailed($this->serverDeployment, $message));
        }

        $totalServerDeployments = $this->siteDeployment->serverDeployments->count();

        if ($success && $this->siteDeployment->serverDeployments->where('completed', 1)->count() == $totalServerDeployments) {
            $this->site->notify(new SiteDeploymentSuccessful($this->siteDeployment));
        }
    }
}
