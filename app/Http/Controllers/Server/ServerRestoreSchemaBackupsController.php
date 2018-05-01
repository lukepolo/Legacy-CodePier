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
    public function store($serverId, $backupId)
    {
        $server = Server::with('backups')->findOrFail($serverId);

        $user = $server->user;

        if ($user->subscribed()) {
            dispatch((
                new RestoreDatabaseBackup($server, $server->backups->find($backupId))
            )->onQueue(
                    config('queue.channels.server_commands')
                ));

            return response()->json('OK');
        }

        return response()->json('You must be subscribed to use backups', 401);
    }
}
