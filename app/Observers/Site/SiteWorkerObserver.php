<?php

namespace App\Observers\Site;

use App\Models\Server\ServerWorker;
use App\Models\Site\SiteWorker;
use App\Traits\ModelCommandTrait;

class SiteWorkerObserver
{
    use ModelCommandTrait;

    /**
     * @param SiteWorker $siteWorker
     */
    public function created(SiteWorker $siteWorker)
    {
        foreach ($siteWorker->site->provisionedServers as $server) {
            if (! ServerWorker::where('user', $siteWorker->user)
                ->where('command', $siteWorker->command)
                ->where('auto_start', $siteWorker->auto_start)
                ->where('auto_restart', $siteWorker->auto_restart)
                ->where('number_of_workers', $siteWorker->number_of_workers)
                ->count()
            ) {
                $serverWorker = new ServerWorker([
                    'server_id' => $server->id,
                    'user' => $siteWorker->user,
                    'command' => $siteWorker->command,
                    'site_worker_id' => $siteWorker->id,
                    'auto_start' => $siteWorker->auto_start,
                    'auto_restart' => $siteWorker->auto_restart,
                    'number_of_workers' => $siteWorker->number_of_workers,
                ]);

                $serverWorker->addHidden([
                    'command' => $this->makeCommand($serverWorker, $siteWorker->site_id),
                ]);

                $serverWorker->save();
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
