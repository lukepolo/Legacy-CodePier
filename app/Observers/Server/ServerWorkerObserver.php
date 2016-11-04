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
        dispatch(new InstallServerWorker($serverWorker));
    }

    /**
     * @param ServerWorker $serverWorker
     * @return bool
     */
    public function deleting(ServerWorker $serverWorker)
    {
        dispatch(new RemoveServerWorker($serverWorker));
        return false;
    }
}
