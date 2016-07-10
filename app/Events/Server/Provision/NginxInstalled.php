<?php

namespace App\Events\Server\Provision;

use App\Events\Event;
use App\Models\Server;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

/**
 * Class NginxInstalled
 * @package App\Events\Server
 */
class NginxInstalled extends Event implements ShouldBroadcastNow
{
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(Server $server)
    {
        $server->status = 'Nginx Installed';
        $server->save();
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
