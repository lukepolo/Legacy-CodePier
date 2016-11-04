<?php

namespace App\Observers\Site;

use App\Models\Server\ServerWorker;
use App\Models\Site\SiteWorker;

/**
 * Class SiteWorkerObserver.
 */
class SiteWorkerObserver
{
    /**
     * @param SiteWorker $siteWorker
     */
    public function created(SiteWorker $siteWorker)
    {
        foreach ($siteWorker->site->provisionedServers as $server) {
            ServerWorker::create([
                'server_id' => $server->id,
                'command' => $siteWorker->command,
                'site_worker_id' => $siteWorker->id,
                'auto_start' => $siteWorker->auto_start,
                'auto_restart' => $siteWorker->auto_restart,
                'number_of_workers' => $siteWorker->number_of_workers,
            ]);
        }
    }

    /**
     * @param SiteWorker $siteWorker
     * @return bool
     */
    public function deleting(SiteWorker $siteWorker)
    {
        $siteWorker->serverWorkers->each(function($serverWorker) {
            $serverWorker->delete();
        });
    }
}
