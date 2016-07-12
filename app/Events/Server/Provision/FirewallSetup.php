<?php

namespace App\Events\Server\Provision;

use App\Events\Event;
use App\Models\Server;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

/**
 * Class FirewallSetup
 * @package App\Events\Server
 */
class FirewallSetup extends Event implements ShouldBroadcastNow
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
        $server->status = 'Firewall Setup';
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
