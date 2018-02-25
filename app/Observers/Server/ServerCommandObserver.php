<?php

namespace App\Observers\Server;

use App\Models\ServerCommand;
use App\Events\Server\ServerCommandUpdated;

class ServerCommandObserver
{
    public function created(ServerCommand $serverCommand)
    {
        broadcast(new ServerCommandUpdated($serverCommand));
        $serverCommand->command->updateStatus();
    }

    /**
     * @param ServerCommand $serverCommand
     */
    public function updated(ServerCommand $serverCommand)
    {
        $serverCommand->command->updateStatus();
        broadcast(new ServerCommandUpdated($serverCommand));
    }
}
