<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Support\Facades\File;

class ReleasedNewVersion implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $version;

    /**
     * Create a new event instance.
     */
    public function __construct()
    {
        $content = File::get(base_path('package.json'));
        $content = json_decode($content);
        $this->version = $content->version;
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
