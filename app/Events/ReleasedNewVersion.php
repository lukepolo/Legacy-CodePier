<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class ReleasedNewVersion implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels, Queueable;

    public $version;

    /**
     * Create a new event instance.
     */
    public function __construct()
    {
        $this->version = current_version();
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
