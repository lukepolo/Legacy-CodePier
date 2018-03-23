<?php

namespace App\Http\Controllers\Server;

use App\Models\Backup;
use App\Models\Server\Server;
use App\Http\Controllers\Controller;
use App\Jobs\Server\RestoreDatabaseBackup;

class ServerRestoreSchemaBackupsController extends Controller
{
    /**
     * @param Server $server
     * @param Backup $backup
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Server $server, Backup $backup)
    {
        $user = $server->user;

        if ($user->subscribed()) {
            dispatch((
            new RestoreDatabaseBackup($server, $backup)
            )->onQueue(
                    config('queue.channels.server_commands')
                ));

            return response()->json('OK');
        }

        return response()->json('You must be subscribed to use backups', 401);
    }
}
