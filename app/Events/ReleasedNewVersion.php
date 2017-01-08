<?php

namespace App\Events;

use Illuminate\Bus\Queueable;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

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
