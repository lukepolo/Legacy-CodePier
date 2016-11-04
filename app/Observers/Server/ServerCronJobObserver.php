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
     */
    public function created(ServerCronJob $serverCronJob)
    {
        dispatch(new InstallServerCronJob($serverCronJob));
    }

    /**
     * @param ServerCronJob $serverCronJob
     * @return bool
     */
    public function deleting(ServerCronJob $serverCronJob)
    {
        dispatch(new RemoveServerCronJob($serverCronJob));
        return false;
    }
}
