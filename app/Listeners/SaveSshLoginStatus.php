<?php

namespace App\Listeners;

use App\Providers\SshLoginAttempted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

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
        $event->server->update([
            'ssh_connection' => $event->state,
        ]);
    }
}
