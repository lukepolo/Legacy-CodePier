<?php

namespace App\Observers\Server;

use App\Jobs\Server\SshKeys\InstallServerSshKey;
use App\Jobs\Server\SshKeys\RemoveServerSshKey;
use App\Models\Server\ServerSshKey;

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
        dispatch(new \App\Jobs\Server\SshKeys\RemoveServerSshKey($serverSshKey));

        return false;
    }
}
