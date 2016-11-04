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
    public function created(ServerCronJob $serverCronJob)
    {
        if (app()->runningInConsole()) {
            dispatch(new InstallServerCronJob($serverCronJob));
        }
    }

    public function deleted(ServerCronJob $serverCronJob)
    {
        if (app()->runningInConsole()) {
            dispatch(new RemoveServerCronJob($serverCronJob));
        }
    }
}
