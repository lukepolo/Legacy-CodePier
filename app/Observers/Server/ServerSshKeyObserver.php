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
     * @return bool
     */
    public function created(ServerSshKey $serverSshKey)
    {
        if (app()->runningInConsole()) {
            dispatch(new InstallServerSshKey($serverSshKey));
            return false;
        }
    }

    /**
     * @param ServerSshKey $serverSshKey
     * @return bool
     */
    public function deleting(ServerSshKey $serverSshKey)
    {
        if (app()->runningInConsole()) {
            dispatch(new RemoveServerSshKey($serverSshKey));
            return false;
        }
    }
}
