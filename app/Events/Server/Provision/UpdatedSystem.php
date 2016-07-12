<?php

namespace App\Events\Server\Provision;

use App\Events\Event;
use App\Models\Server;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

/**
 * Class UpdatedSystem
 * @package App\Events\Server
 */
class UpdatedSystem extends Event implements ShouldBroadcastNow
{
    use SerializesModels;

    public $server;
    public $donePercentage;

    /**
     * Create a new event instance.
     * @param Server $server
     * @param $donePercentage
     */
    public function __construct(Server $server, $donePercentage)
    {
        $server->status = 'System Updated';
        $server->save();
        
        $this->server = $server;
        $this->donePercentage = $donePercentage;
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
