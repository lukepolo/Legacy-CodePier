<?php

namespace App\Events\Server;

use App\Events\Event;
use App\Models\Server;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

/**
 * Class ComposerInstalled
 * @package App\Events\Server
 */
class ComposerInstalled extends Event implements ShouldBroadcastNow
{
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(Server $server)
    {
        //
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
