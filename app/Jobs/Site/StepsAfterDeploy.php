<?php

namespace App\Jobs\Site;

use App\Models\Site\Site;
use Illuminate\Bus\Queueable;
use App\Models\Server\Server;
use App\Models\Site\SiteDeployment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\Site\SiteDeploymentSuccessful;
use App\Contracts\Site\SiteServiceContract as SiteService;

class StepsAfterDeploy implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $site;
    public $server;
    public $siteDeployment;

    public $tries = 1;
    public $timeout = 60;

    /**
     * Create a new job instance.
     *
     * @param Site $site
     * @param Server $server
     * @param SiteDeployment $siteDeployment
     */
    public function __construct(Site $site, Server $server, SiteDeployment $siteDeployment)
    {
        $this->site = $site;
        $this->server = $server;
        $this->siteDeployment = $siteDeployment;
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Site\SiteService | SiteService $siteService
     */
    public function handle(SiteService $siteService)
    {
        $success = true;
        $startTime = microtime(true);

        foreach ($this->siteDeployment->serverDeployments as $siteServerDeployment) {
            try {
                $siteService->runStepsAfterDeploy($this->server, $this->site, $siteServerDeployment, $this->siteDeployment);
            } catch (\Exception $e) {
                $message = $e->getMessage();

                if (get_class($e) == \Exception::class) {
                    app('sentry')->captureException($e);
                    $message = 'The error has been reported and we are looking into it.';
                }
                $siteService->deployFailed($this->site, $this->server, $siteServerDeployment, $message, $startTime);
                $success = false;
            }
        }

        if ($success) {
            $this->site->notify(new SiteDeploymentSuccessful($this->siteDeployment));
        }
    }
}
