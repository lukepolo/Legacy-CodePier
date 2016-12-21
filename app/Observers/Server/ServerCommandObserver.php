<?php

namespace App\Observers\Server;

use App\Models\ServerCommand;
use App\Events\Server\ServerCommandUpdated;

class ServerCommandObserver
{
    public function created(ServerCommand $serverCommand)
    {
        event(new ServerCommandUpdated($serverCommand));
        $serverCommand->command->updateStatus();
    }

    /**
     * @param ServerCommand $serverCommand
     */
    public function updated(ServerCommand $serverCommand)
    {
        $serverCommand->command->updateStatus();
        event(new ServerCommandUpdated($serverCommand));
    }
}
