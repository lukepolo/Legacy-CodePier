<?php

namespace App\Observers\Server;

use App\Models\Server\ServerCronJob;

class ServerCronJobObserver
{
    /**
     * @param ServerCronJob $serverCronJob
     */
    public function created(ServerCronJob $serverCronJob)
    {
        dispatch(new \App\Jobs\Server\CronJobs\InstallServerCronJob($serverCronJob));
    }

    /**
     * @param ServerCronJob $serverCronJob
     * @return bool
     */
    public function deleting(ServerCronJob $serverCronJob)
    {
        dispatch(new \App\Jobs\Server\CronJobs\RemoveServerCronJob($serverCronJob));

        return false;
    }
}
