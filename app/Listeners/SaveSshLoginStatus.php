<?php

namespace App\Listeners;

use App\Events\SshLoginAttempted;

class SaveSshLoginStatus
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SshLoginAttempted  $event
     * @return void
     */
    public function handle(SshLoginAttempted $event)
    {
        $server = $event->server->first();
        $server->ssh_connection = $event->state;
        $server->save();
    }
}
