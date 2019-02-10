<?php

namespace App\Observers\Server;

use Carbon\Carbon;
use App\Models\Server\Server;

class ServerObserver
{
    /**
     * @param Server $server
     */
    public function creating(Server $server)
    {
        $server->generateSudoPassword();
        $server->generateDatabasePassword();
    }

    public function deleting(Server $server)
    {
        $server->detachDeploymentSteps();
    }
}
