<?php

namespace App\Observers\Site;

use App\Models\Site\Site;
use App\Models\FirewallRule;
use App\Jobs\Site\DeleteSite;
use App\Traits\ModelCommandTrait;
use App\Events\Site\SiteSshKeyDeleted;
use App\Events\Site\SiteWorkerDeleted;
use App\Events\Site\SiteCronJobDeleted;
use App\Events\Site\SiteUpdatedWebConfig;
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

        $this->repositoryService->generateNewSshKeys($site);
    }

    public function updating(Site $site)
    {
        remove_events($site);

        if ($site->isDirty('framework')) {
            $tempSite = clone $site;

            $tempSite->framework = $site->getOriginal('framework');

            if (! empty($tempSite->framework)) {
                foreach ($this->siteFeatureService->getSuggestedCronJobs($tempSite) as $cronJob) {
                    foreach ($site->cronJobs as $siteCronJob) {
                        if ($siteCronJob->job == $cronJob) {
                            $site->cronJobs()->detach($siteCronJob);
                        }
                    }
                }
            }
        }

        if ($site->isDirty('web_directory')) {
            event(new SiteUpdatedWebConfig($site));
        }

        if ($site->isDirty('type') || $site->isDirty('framework')) {
            $site->deploymentSteps()->delete();
            $this->siteDeploymentStepsService->saveDefaultSteps($site);
            $this->siteFeatureService->saveSuggestedFeaturesDefaults($site);
            $this->siteFeatureService->saveSuggestedCronJobs($site);
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
