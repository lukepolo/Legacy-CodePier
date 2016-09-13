<?php

namespace App\Events;

use App\Models\Server;
use Illuminate\Queue\SerializesModels;

/**
 * Class ServerProvisioned.
 */
class ServerProvisioned
{
    use SerializesModels;

    public $user;
    public $server;
    public $sudoPassword;
    public $databasePassword;

    /**
     * Create a new event instance.
     *
     * @param Server $server
     * @param $sudoPassword
     * @param $databasePassword
     */
    public function __construct(Server $server, $sudoPassword, $databasePassword)
    {
        $this->server = $server;
        $this->user = $server->user;
        $this->sudoPassword = $sudoPassword;
        $this->databasePassword = $databasePassword;
    }
}
