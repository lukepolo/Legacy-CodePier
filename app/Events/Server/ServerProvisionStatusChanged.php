<?php

namespace App\Events\Server;

use App\Models\Server\Server;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class ServerProvisionStatusChanged implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $server;
    public $serverCurrentProvisioningStep;

    /**
     * Create a new event instance.
     *
     * @param Server $server
     * @param $status
     * @param $progress
     */
    public function __construct(Server $server, $status, $progress)
    {
        $server->progress = $progress;
        $server->status = $status;
        $server->ip;

        $server->save();

        $this->server = $server;
        $this->serverCurrentProvisioningStep = $server->currentProvisioningStep();
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
        unset($this->server->server_features);

        return [
            'server' => strip_relations($this->server),
            'serverCurrentProvisioningStep' => $this->serverCurrentProvisioningStep
        ];
    }
}
