<?php

namespace App\Events\Server;

use App\Models\Server\Server;
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
        $command = json_decode($this->serverCommand->command->toJson());

        unset($command->server_commands);

        return [
            'command' => $command,
        ];
    }
}
