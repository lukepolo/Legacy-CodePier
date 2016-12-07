<?php

namespace App\Observers\Server;

use App\Models\Server\ServerWorker;
use App\Jobs\Server\Workers\RemoveServerWorker;
use App\Jobs\Server\Workers\InstallServerWorker;

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
