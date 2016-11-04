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
        dispatch(new InstallServerSshKey($serverSshKey));
    }

    /**
     * @param ServerSshKey $serverSshKey
     * @return bool
     */
    public function deleting(ServerSshKey $serverSshKey)
    {
        dispatch(new RemoveServerSshKey($serverSshKey));

        return false;
    }
}
