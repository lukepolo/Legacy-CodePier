<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class ReleasedNewVersion implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $version;

    /**
     * Create a new event instance.
     */
    public function __construct()
    {
        $this->version = exec('git rev-parse --short HEAD');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('app');
    }
}
