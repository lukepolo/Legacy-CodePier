<?php

namespace App\Observers\Server;

use App\Events\Server\ServerCommandUpdated;
use App\Models\ServerCommand;

class ServerCommandObserver
{
    /**
     * @param ServerCommand $serverCommand
     */
    public function updated(ServerCommand $serverCommand)
    {
        event(new ServerCommandUpdated($serverCommand));
    }
}
