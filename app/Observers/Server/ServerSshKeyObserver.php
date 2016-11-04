<?php

namespace App\Observers\Server;

use App\Jobs\Server\InstallServerSshKey;
use App\Jobs\Server\RemoveServerSshKey;
use App\Models\Server\ServerSshKey;

/**
 * Class ServerSshKeyObserver.
 */
class ServerSshKeyObserver
{
    /**
     * @param ServerSshKey $serverSshKey
     */
    public function created(ServerSshKey $serverSshKey)
    {
        if(app()->runningInConsole()) {
            dispatch(new InstallServerSshKey($serverSshKey));
        }
    }

    /**
     * @param ServerSshKey $serverSshKey
     */
    public function deleted(ServerSshKey $serverSshKey)
    {
        if(app()->runningInConsole()) {
            dispatch(new RemoveServerSshKey($serverSshKey));
        }
    }
}
