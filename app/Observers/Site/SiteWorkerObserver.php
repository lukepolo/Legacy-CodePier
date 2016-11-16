<?php

namespace App\Observers\Site;

use App\Models\Server\ServerWorker;
use App\Models\Site\SiteWorker;

class SiteWorkerObserver
{
    /**
     * @param SiteWorker $siteWorker
     */
    public function created(SiteWorker $siteWorker)
    {
        foreach ($siteWorker->site->provisionedServers as $server) {

            if(!ServerWorker::where('user', $siteWorker->user)
                ->where('command', $siteWorker->command)
                ->where('auto_start', $siteWorker->auto_start)
                ->where('auto_restart', $siteWorker->auto_restart)
                ->where('number_of_workers', $siteWorker->number_of_workers)
                ->count()
            ) {
                ServerWorker::create([
                    'server_id' => $server->id,
                    'user' => $siteWorker->user,
                    'command' => $siteWorker->command,
                    'site_worker_id' => $siteWorker->id,
                    'auto_start' => $siteWorker->auto_start,
                    'auto_restart' => $siteWorker->auto_restart,
                    'number_of_workers' => $siteWorker->number_of_workers,
                ]);
            }
        }
    }

    /**
     * @param SiteWorker $siteWorker
     * @return bool
     */
    public function deleting(SiteWorker $siteWorker)
    {
        $siteWorker->serverWorkers->each(function ($serverWorker) {
            $serverWorker->delete();
        });
    }
}
