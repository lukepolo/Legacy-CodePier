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
    public $server;
    public $sudoPassword;
    public $databasePassword;

    /**
     * Create the event listener.
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
     * Handle the event.
     *
     * @param  ServerProvisioned $event
     * @return void
     */
    public function handle(ServerProvisioned $event)
    {
        \Mail::queue('emails.sudoAndDatabasePasswords', [
            'server_ip' => $event->server->ip,
            'sudoPassword' => $event->sudoPassword,
            'databasePassword' => $event->databasePassword
        ], function ($message) {
            $message->to(\Auth::user()->email);
            $message->subject('CodePier Server Provisioned - Details Inside (IMPORTANT INFORMATION)');
        });
    }
}
