<?php

namespace App\Listeners;

use App\Events\ServerProvisioned;
use App\Models\Server;

/**
 * Class EmailSudoAndDatabasePasswords
 * @package App\Listeners
 */
class EmailSudoAndDatabasePasswords
{
    /**
     * Handle the event.
     *
     * @param  ServerProvisioned $event
     * @return void
     */
    public function handle(ServerProvisioned $event)
    {
        $user = $event->server->user;

        \Mail::queue('emails.sudoAndDatabasePasswords', [
            'serverIp' => $event->server->ip,
            'sudoPassword' => $event->sudoPassword,
            'databasePassword' => $event->databasePassword
        ], function ($message) use($user) {
            $message->to($user->email);
            $message->subject('CodePier Server Provisioned');
        });
    }
}
