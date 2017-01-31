<?php

namespace App\Observers\Site;

use App\Models\Site\Site;
use App\Models\FirewallRule;
use App\Jobs\Site\DeleteSite;
use App\Traits\ModelCommandTrait;
use App\Jobs\Site\UpdateWebConfig;
use App\Jobs\Site\RenameSiteDomain;
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
     * @param \App\Services\Site\SiteFeatureService |SiteFeatureService $siteFeatureService
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
    }

    public function updating(Site $site)
    {
        if ($site->isDirty('domain')) {
            dispatch(
                (new RenameSiteDomain($site, $site->domain, $site->getOriginal('domain')))->onQueue(config('queue.channels.server_commands'))
            );
        }

        if ($site->isDirty('framework')) {
            $tempSite = clone $site;

            $tempSite->framework = $site->getOriginal('framework');

            foreach ($this->siteFeatureService->getSuggestedCronJobs($tempSite) as $cronJob) {
                foreach ($site->cronJobs as $siteCronJob) {
                    if ($siteCronJob->job == $cronJob) {
                        $siteCronJob->delete();
                    }
                }
            }
        }
    }

    public function updated(Site $site)
    {
        remove_events($site);

        if ($site->isDirty('repository')) {
            $site->private = false;
            $site->public_ssh_key = null;
            $site->private_ssh_key = null;
            $site->save();
        }

        if ($site->isDirty('web_directory')) {
            foreach ($site->provisionedServers as $server) {
                dispatch(
                    (new UpdateWebConfig($server, $site))->onQueue(config('queue.channels.server_commands'))
                );
            }
        }

        if ($site->repository && $site->deploymentSteps->isEmpty()) {
            $this->siteDeploymentStepsService->saveDefaultSteps($site);
            $this->siteFeatureService->saveSuggestedFeaturesDefaults($site);
        }

        if ($site->isDirty('framework')) {
            $this->siteFeatureService->saveSuggestedCronJobs($site);
        }
    }

    /**
     * @param Site $site
     */
    public function deleting(Site $site)
    {
        // We need to trigger the delete events for some
        // of the relations so they trickle down
        $site->files->each(function ($file) {
            $file->delete();
        });

        $site->workers->each(function ($worker) {
            $worker->delete();
        });

        $site->cronJobs()->each(function ($cronJob) {
            $cronJob->delete();
        });

        $site->deployments()->delete();
    }

    public function deleted(Site $site)
    {
        foreach ($site->provisionedServers as $server) {
            dispatch(
                (new DeleteSite($server, $site))->onQueue(config('queue.channels.server_commands'))
            );
        }
    }
}
