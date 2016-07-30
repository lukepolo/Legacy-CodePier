<?php

namespace App\Events\Server;

use App\Events\Event;
use App\Models\Server;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * Class ServerCreated
 * @package App\Events\Server
 */
class ServerProvisionStatusChanged extends Event implements ShouldBroadcastNow
{
    use SerializesModels;

    public $status;
    public $serverID;
    public $progress;

    private $user;
    /**
     * Create a new event instance.
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
        $server->save();
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['user.'.$this->user->id];
    }
}
