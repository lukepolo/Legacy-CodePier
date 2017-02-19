<?php

namespace App\Observers\Site;

use App\Models\CronJob;
use App\Models\Site\Site;
use App\Models\FirewallRule;
use App\Jobs\Site\DeleteSite;
use App\Traits\ModelCommandTrait;
use App\Jobs\Site\UpdateWebConfig;
use App\Jobs\Site\RenameSiteDomain;
use App\Events\Site\SiteWorkerDeleted;
use App\Events\Site\SiteCronJobCreated;
use App\Events\Site\SiteCronJobDeleted;
use App\Events\Sites\SiteSshKeyDeleted;
use App\Events\Site\SiteFirewallRuleDeleted;
use App\Events\Site\SiteSslCertificateDeleted;
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

    public function creating(Site $site)
    {
        $site->hash = create_redis_hash();
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
                        $site->cronJobs()->detach($siteCronJob);
                    }
                }
            }

            foreach ($this->siteFeatureService->getSuggestedCronJobs($site) as $cronJob) {
                $cronJob = CronJob::create([
                    'user' => 'codepier',
                    'job' => $cronJob,
                ]);

                $site->cronJobs()->save($cronJob);

                event(new SiteCronJobCreated($site, $cronJob));
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
        $site->workers()->each(function ($worker) use ($site) {
            event(new SiteWorkerDeleted($site, $worker));
        });

        $site->cronJobs()->each(function ($cronJob) use ($site) {
            event(new SiteCronJobDeleted($site, $cronJob));
        });

        $site->firewallRules()->each(function ($firewallRule) use ($site) {
            event(new SiteFirewallRuleDeleted($site, $firewallRule));
        });

        $site->sshKeys()->each(function ($sshKey) use ($site) {
            event(new SiteSshKeyDeleted($site, $sshKey));
        });

        $site->sslCertificates()->each(function ($sslCertificate) use ($site) {
            event(new SiteSslCertificateDeleted($site, $sslCertificate));
        });

        $site->buoys()->delete();
        $site->commands()->delete();
        $site->deployments()->delete();
        $site->deploymentSteps()->delete();
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
