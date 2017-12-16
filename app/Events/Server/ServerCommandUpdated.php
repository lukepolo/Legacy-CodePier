<?php

namespace App\Events\Server;

use App\Models\ServerCommand;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class ServerCommandUpdated implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $serverCommand;

    /**
     * Create a new event instance.
     *
     * @param ServerCommand $serverCommand
     */
    public function __construct(ServerCommand $serverCommand)
    {
        $this->serverCommand = $serverCommand;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('App.Models.Server.Server.'.$this->serverCommand->server->id);
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        $command = $this->serverCommand->command;

        $command->load('serverCommands.server');

        if (strlen($command->log) >= 6000) {
            $command->log = substr($command->log, 0, 6000)."\n , please reload to see the rest of the log";
        }

        return [
            'command' => strip_relations($command),
        ];
    }
}
