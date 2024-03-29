<?php

namespace App\Observers\Site;

use App\Models\Site\Site;
use App\Models\FirewallRule;
use App\Jobs\Site\DeleteSite;
use App\Traits\ModelCommandTrait;
use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Contracts\Site\SiteFeatureServiceContract as SiteFeatureService;
use App\Contracts\Repository\RepositoryServiceContract as RepositoryService;
use App\Contracts\Site\SiteDeploymentStepsServiceContract as SiteDeploymentStepsService;

class SiteObserver
{
    use ModelCommandTrait;

    public static $originalServers = [];

    private $siteService;
    private $repositoryService;
    private $siteFeatureService;
    private $siteDeploymentStepsService;

    /**
     * SiteObserver constructor.
     *
     * @param \App\Services\Site\SiteService | SiteService $siteService
     * @param \App\Services\Repository\RepositoryService | RepositoryService $repositoryService
     * @param \App\Services\Site\SiteFeatureService | SiteFeatureService $siteFeatureService
     * @param \App\Services\Site\SiteDeploymentStepsService | SiteDeploymentStepsService $siteDeploymentStepsService
     */
    public function __construct(
        SiteService $siteService,
        RepositoryService $repositoryService,
        SiteFeatureService $siteFeatureService,
        SiteDeploymentStepsService $siteDeploymentStepsService
    ) {
        $this->siteService = $siteService;
        $this->repositoryService = $repositoryService;
        $this->siteFeatureService = $siteFeatureService;
        $this->siteDeploymentStepsService = $siteDeploymentStepsService;
    }

    public function creating(Site $site)
    {
        $site->hash = unique_hash();
        Site::withTrashed()->count();
        $site->port = Site::STARTING_PORT + Site::withTrashed()->count();
    }

    public function created(Site $site)
    {
        $site->firewallRules()->save(
            FirewallRule::create([
                'description' => 'HTTP',
                'port'        => '80',
                'from_ip'     => null,
            ])
        );

        $site->firewallRules()->save(
            FirewallRule::create([
                'description' => 'HTTPS',
                'port'        => '443',
                'from_ip'     => null,
            ])
        );

        $this->repositoryService->generateNewSshKeys($site);
    }

    public function updating(Site $site)
    {
        if ($site->isDirty('type') || $site->isDirty('framework')) {
            if ($site->type === 'Swift') {
                $site->zero_downtime_deployment = false;
            }

            $site->server_features = $this->siteFeatureService->getSuggestedFeaturesDefaults($site);
            $this->siteFeatureService->detachSuggestedCronJobs($site);
            $this->siteFeatureService->detachSuggestedFiles($site);
            $this->siteFeatureService->detachSuggestedFiles($site, true);
            $site->deploymentSteps()->delete();
        } elseif ($site->isDirty('zero_downtime_deployment')) {
            $this->siteFeatureService->detachSuggestedCronJobs($site);
        } elseif (json_encode($site->server_features) !== json_encode(json_decode($site->getOriginal('server_features'), true))) {
            $this->siteFeatureService->detachSuggestedFiles($site);
        }
    }

    public function updated(Site $site)
    {
        if ($site->isDirty('type') || $site->isDirty('framework')) {
            $site->refresh();
            $this->siteDeploymentStepsService->saveDefaultSteps($site);
            $this->siteFeatureService->saveSuggestedCronJobs($site);
            $this->siteFeatureService->saveSuggestedFiles($site);
        } elseif ($site->isDirty('zero_downtime_deployment')) {
            $this->siteFeatureService->saveSuggestedCronJobs($site);
        }

        if (json_encode($site->server_features) !== json_encode(json_decode($site->getOriginal('server_features'), true))) {
            $site->refresh();
            $this->siteFeatureService->saveSuggestedFiles($site);
        }
    }

    /**
     * @param Site $site
     * @throws \Exception
     */
    public function deleting(Site $site)
    {
        $site->buoys()->delete();
        $site->deployments()->delete();
        $site->deploymentSteps()->delete();
    }

    public function deleted(Site $site)
    {
        foreach ($site->provisionedServers as $server) {
            dispatch(
                (new DeleteSite($server, $site))
                    ->onQueue(config('queue.channels.server_commands'))
            );
        }
    }
}
