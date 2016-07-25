<?php

namespace App\Events;

use App\Models\Server;
use Illuminate\Queue\SerializesModels;

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
     * @param $errors
     */
    public function __construct(Server $server, $sudoPassword, $databasePassword, $log, $errors)
    {
        $this->server = $server;
        $this->sudoPassword = $sudoPassword;
        $this->databasePassword = $databasePassword;
        $this->errors = $errors;

        \App\Models\Event::create([
            'event_id' => $server->id,
            'event_type' => Server::class,
            'description' => 'Server Provisioned',
            'data' => $errors,
            'log' => $log,
            'internal_type' => 'provision_status'
        ]);
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
