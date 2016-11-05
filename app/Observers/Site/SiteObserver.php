<?php

namespace App\Observers\Site;

use App\Contracts\Repository\RepositoryServiceContract as RepositoryService;
use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Models\Site\Site;

class SiteObserver
{
    public static $originalServers = [];

    private $siteService;
    private $repositoryService;

    /**
     * SiteObserver constructor.
     *
     * @param \App\Services\Site\SiteService | SiteService                        $siteService
     * @param \App\Services\Repository\RepositoryService | RepositoryService $repositoryService
     */
    public function __construct(SiteService $siteService, RepositoryService $repositoryService)
    {
        $this->siteService = $siteService;
        $this->repositoryService = $repositoryService;
    }

    /**
     * @param Site $site
     */
    public function saved(Site $site)
    {
        if (! empty($site->repository)) {
            $this->repositoryService->importSshKeyIfPrivate($site);
        }

        foreach ($site->provisionedServers() as $server) {
            $this->siteService->create($server, $site);
        }
    }

    /**
     * @param Site $site
     */
    public function deleting(Site $site)
    {
        // We need to trigger the delete events for some
        // of the relations so they trickle down
        $site->ssls->each(function ($ssl) {
            $ssl->delete();
        });

        $site->files->each(function ($file) {
            $file->delete();
        });

        $site->workers->each(function ($worker) {
            $worker->delete();
        });

        $site->sshKeys()->each(function ($sshKey) {
            $sshKey->delete();
        });

        $site->cronJobs()->each(function ($cronJob) {
            $cronJob->delete();
        });

        $site->firewallRules()->each(function ($firewallRule) {
            $firewallRule->delete();
        });


        $site->deployments()->delete();
        $site->deploymentSteps()->delete();
    }
}
