<?php

namespace App\Events\Site;

use App\Models\Site\Lifeline;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class LifeLineUpdated implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $lifeline;

    /**
     * Create a new event instance.
     *
     * @param Lifeline $lifeline
     */
    public function __construct(Lifeline $lifeline)
    {
        $this->lifeline = $lifeline;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('App.Models.Site.Lifeline.'.$this->lifeline->id);
    }
}
