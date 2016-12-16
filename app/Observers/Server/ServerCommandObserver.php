<?php

namespace App\Observers\Server;

use App\Models\ServerCommand;
use App\Events\Server\ServerCommandUpdated;

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
