<?php

namespace App\Events\Server;

use App\Models\Server;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

/**
 * Class ServerCreated.
 */
class ServerProvisionStatusChanged implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $status;
    public $serverID;
    public $progress;
    public $ip;
    public $connected;

    private $user;

    /**
     * Create a new event instance.
     *
     * @param Server $server
     * @param $status
     * @param $progress
     */
    public function __construct(Server $server, $status, $progress)
    {
        $this->user = $server->user;

        $this->serverID = $server->id;
        $this->progress = $server->progress = $progress;
        $this->status = $server->status = $status;
        $this->ip = $server->ip;
        $this->connected = $server->ssh_connection;

        $server->save();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('App.Models.Server.'.$this->serverID);
    }
}
