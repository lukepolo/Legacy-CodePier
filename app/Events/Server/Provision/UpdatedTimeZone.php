<?php

namespace App\Events\Server;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;

/**
 * Class UpdatedSystem
 * @package App\Events\Server
 */
class UpdatedTimeZone extends Event
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
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
