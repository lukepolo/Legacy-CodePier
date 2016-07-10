<?php

namespace App\Events\Server\Provision;

use App\Events\Event;
use App\Models\Server;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

/**
 * Class MemcachedInstalled
 * @package App\Events\Server
 */
class MemcachedInstalled extends Event implements ShouldBroadcastNow
{
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(Server $server)
    {
        $server->status = 'Memcached Installed';
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
