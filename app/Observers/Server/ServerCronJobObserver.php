<?php

namespace App\Observers\Server;

use App\Jobs\Server\InstallServerCronJob;
use App\Jobs\Server\RemoveServerCronJob;
use App\Models\Server\ServerCronJob;

/**
 * Class ServerCronJobObserver.
 */
class ServerCronJobObserver
{
    /**
     * @param ServerCronJob $serverCronJob
     * @return bool
     */
    public function created(ServerCronJob $serverCronJob)
    {
        if (app()->runningInConsole()) {
            dispatch(new InstallServerCronJob($serverCronJob));

            return false;
        }
    }

    /**
     * @param ServerCronJob $serverCronJob
     * @return bool
     */
    public function deleting(ServerCronJob $serverCronJob)
    {
        if (app()->runningInConsole()) {
            dispatch(new RemoveServerCronJob($serverCronJob));

            return false;
        }
    }
}
