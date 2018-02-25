<?php

namespace App\Observers\Server;

use App\Events\Server\ServerCommandUpdated;
use App\Models\ServerCommand;

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
