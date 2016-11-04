<?php

namespace App\Observers\Server;

use App\Models\Server\Server;

/**
 * Class ServerObserver.
 */
class ServerObserver
{
    /**
     * @param Server $server
     */
    public function created(Server $server)
    {
    }

    /**
     * @param Server $server
     */
    public function deleting(Server $server)
    {
    }
}
