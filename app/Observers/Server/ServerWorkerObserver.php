<?php

namespace App\Observers\Server;

use App\Jobs\Server\InstallServerWorker;
use App\Jobs\Server\RemoveServerWorker;
use App\Models\Server\ServerWorker;

/**
 * Class ServerWorkerObserver.
 */
class ServerWorkerObserver
{
    /**
     * @param ServerWorker $serverWorker
     */
    public function created(ServerWorker $serverWorker)
    {
        if (app()->runningInConsole()) {
            dispatch(new InstallServerWorker($serverWorker));
        }
    }

    /**
     * @param ServerWorker $serverWorker
     */
    public function deleted(ServerWorker $serverWorker)
    {
        if (app()->runningInConsole()) {
            dispatch(new RemoveServerWorker($serverWorker));
        }
    }
}
