<?php

namespace App\Jobs\Site;

use App\Models\Site\Site;
use Illuminate\Bus\Queueable;
use App\Exceptions\DeploymentFailed;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\Site\DeploymentStepFailed;
use App\Models\Site\SiteServerDeployment;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Site\SiteDeploymentFailed;
use App\Notifications\Site\SiteDeploymentSuccessful;
use App\Contracts\Site\SiteServiceContract as SiteService;

class Deploy implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $site;
    public $server;
    public $siteDeployment;
    public $serverDeployment;
    public $oldSiteDeployment;

    public $tries = 1;
    public $timeout = 300;

    /**
     * Create a new job instance.
     * @param SiteServerDeployment $serverDeployment
     * @param null $oldSiteDeployment
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
     * @throws DeploymentFailed
     */
    public function handle(SiteService $siteService)
    {
        $success = true;

        try {
            $siteService->deploy($this->server, $this->site, $this->serverDeployment, $this->oldSiteDeployment);
        } catch (\Exception $e) {

            \Log::error($e);

            $message = $e->getMessage();

            if (get_class($e) == \Exception::class) {
                app('sentry')->captureException($e);
                $message = 'The error has been reported and we are looking into it.';
            }

            $success = false;

            $event = $this->serverDeployment->events->first(function ($event) {
                return $event->completed == false || $event->failed;
            });

            if (! $event) {
                $event = $this->serverDeployment->events->last();
            }

            if (! $event->failed) {
                event(new DeploymentStepFailed($this->site, $this->server, $event, $event->step, $message));
            }

            $this->site->notify(new SiteDeploymentFailed($this->serverDeployment, $message));
        }

        $totalServerDeployments = $this->siteDeployment->serverDeployments->count();

        if ($success && $this->siteDeployment->serverDeployments->where('completed', 1)->count() == $totalServerDeployments) {
            $this->site->notify(new SiteDeploymentSuccessful($this->siteDeployment));
        }
    }
}
