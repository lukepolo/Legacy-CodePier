<?php

namespace App\Observers\Site;

use App\Contracts\Repository\RepositoryServiceContract as RepositoryService;
use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Models\Site\Site;

/**
 * Class SiteObserver.
 */
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
        $site->ssls()->delete();
        $site->files()->delete();
        $site->workers()->delete();
        $site->sshKeys()->delete();
        $site->cronJobs()->delete();
        $site->deployments()->delete();
        $site->firewallRules()->delete();
        $site->deploymentSteps()->delete();
    }
}
