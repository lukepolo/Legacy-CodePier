<?php

namespace App\Jobs\Site;

use App\Events\Site\DeploymentStepFailed;
use App\Models\Site\Site;
use Illuminate\Bus\Queueable;
use App\Models\Site\SiteDeployment;
use App\Exceptions\DeploymentFailed;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Site\SiteServerDeployment;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\Site\NewSiteDeployment;
use App\Notifications\Site\SiteDeploymentFailed;
use App\Notifications\Site\SiteDeploymentSuccessful;
use App\Contracts\Site\SiteServiceContract as SiteService;

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
                $siteService->deploy($serverDeployment->server, $this->site, $serverDeployment, $this->sha);
            } catch(\Exception $e) {

                $message = $e->getMessage();

                if(get_class($e) == \Exception::class) {
                    if(env('APP_ENV') == 'production') {
                        app('sentry')->captureException($e);
                    }

                    $message = 'The error has been reported and we are looking into it.';
                }

                $success = false;

                $event = $serverDeployment->events->first(function($event) {
                    return $event->completed == false;
                });

                event(new DeploymentStepFailed($this->site, $serverDeployment->server, $event, $event->step, [$message]));
                $this->site->notify(new SiteDeploymentFailed($serverDeployment, $message));
            }
        }

        if ($success) {
            $this->site->notify(new SiteDeploymentSuccessful($this->siteDeployment));
        }
    }
}
