<?php

namespace App\Jobs\Site;

use App\Models\Site\Site;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Site\SiteServerDeployment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\Site\SiteDeploymentSuccessful;
use App\Contracts\Site\SiteServiceContract as SiteService;

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
     * @param Site $site
     * @param Server $server
     * @param SiteServerDeployment $serverDeployment
     * @param null $oldSiteDeployment
     */
    public function __construct(Site $site, Server $server, SiteServerDeployment $serverDeployment, $oldSiteDeployment = null)
    {
        $this->site = $site;
        $this->server = $server;
        $this->serverDeployment = $serverDeployment;

        $this->serverDeployment->load([
            'events',
            'siteDeployment.serverDeployments',
        ]);

        $this->oldSiteDeployment = $oldSiteDeployment;
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
        $startTime = microtime(true);

        try {
            $siteService->deploy($this->server, $this->site, $this->serverDeployment, $this->oldSiteDeployment);
        } catch (\Exception $e) {
            $message = $e->getMessage();

            if (get_class($e) == \Exception::class) {
                app('sentry')->captureException($e);
                $message = 'The error has been reported and we are looking into it.';
            }
            $siteService->deployFailed($this->site, $this->server, $this->serverDeployment, $message, $startTime);
            $success = false;
        }

        $totalServerDeployments = $this->siteDeployment->serverDeployments->count();

        $this->siteDeployment->refresh();

        if ($success && $this->siteDeployment->serverDeployments->where('completed', 1)->count() == $totalServerDeployments) {
            $this->site->notify(new SiteDeploymentSuccessful($this->siteDeployment));
        } elseif ($success && $this->siteDeployment->serverDeployments->where('pending_complete', 1)->count() == $totalServerDeployments) {
            dispatch_now(new StepsAfterDeploy($this->site, $this->server, $this->siteDeployment));
        }
    }
}
