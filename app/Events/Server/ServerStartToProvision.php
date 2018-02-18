<?php

namespace App\Events\Server;

use App\Models\Server\Server;
use App\Models\ServerCommand;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class ServerStartToProvision implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $server;

    /**
     * Create a new event instance.
     *
     * @param Server $server
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('App.Models.Server.Server.'.$this->server->id);
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        $this->server->load('provisionSteps');

        return [
            'server' => $this->server,
        ];
    }
}
