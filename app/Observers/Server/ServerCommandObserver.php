<?php

namespace App\Observers\Server;

use App\Models\ServerCommand;
use App\Events\Server\ServerCommandUpdated;

class ServerCommandObserver
{
    public function created(ServerCommand $serverCommand)
    {
        event(new ServerCommandUpdated($serverCommand));
    }

    /**
     * @param ServerCommand $serverCommand
     */
    public function updated(ServerCommand $serverCommand)
    {
        event(new ServerCommandUpdated($serverCommand));
    }
}
