<?php

namespace App\Observers\Server;

use App\Models\Server\ServerSshKey;
use App\Jobs\Server\SshKeys\RemoveServerSshKey;
use App\Jobs\Server\SshKeys\InstallServerSshKey;

class ServerSshKeyObserver
{
    /**
     * @param ServerSshKey $serverSshKey
     */
    public function created(ServerSshKey $serverSshKey)
    {
        dispatch(
            (new InstallServerSshKey($serverSshKey))->onQueue(env('SERVER_COMMAND_QUEUE'))
        );
    }

    /**
     * @param ServerSshKey $serverSshKey
     * @return bool
     */
    public function deleting(ServerSshKey $serverSshKey)
    {
        dispatch(
            (new RemoveServerSshKey($serverSshKey))->onQueue(env('SERVER_COMMAND_QUEUE'))
        );

        return false;
    }
}
