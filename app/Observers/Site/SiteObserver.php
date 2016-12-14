<?php

namespace App\Observers\Site;

use App\Models\Site\Site;
use App\Traits\ModelCommandTrait;
use App\Jobs\Site\RenameSiteDomain;
use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Contracts\Repository\RepositoryServiceContract as RepositoryService;

class SiteObserver
{
    use ModelCommandTrait;

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

    public function updating(Site $site)
    {
        $dirty = $site->getDirty();
        if (isset($dirty['domain'])) {
            dispatch(
                (new RenameSiteDomain($site, $site->domain, $site->getOriginal('domain')))->onQueue(env('SERVER_COMMAND_QUEUE'))
            );
        }
    }

    public function updated(Site $site) {
        $dirty = $site->getDirty();
        if(isset($dirty['repository'])) {
            $site->private = false;
            $site->public_ssh_key = null;
            $site->private_ssh_key = null;
            save_without_events($site);
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
}
