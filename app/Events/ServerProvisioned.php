<?php

namespace App\Events;

use App\Events\Event;
use App\Models\Server;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * Class ServerProvisioned
 * @package App\Events
 */
class ServerProvisioned extends Event
{
    use SerializesModels;

    public $server;
    public $sudoPassword;
    public $databasePassword;

    /**
     * Create a new event instance.
     * @param Server $server
     * @param $sudoPassword
     * @param $databasePassword
     */
    public function __construct(Server $server, $sudoPassword, $databasePassword)
    {
        $this->server = $server;
        $this->sudoPassword = $sudoPassword;
        $this->databasePassword = $databasePassword;
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
