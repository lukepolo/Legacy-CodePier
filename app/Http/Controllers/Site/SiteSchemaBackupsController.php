<?php

namespace App\Http\Controllers\Site;

use App\Jobs\Server\BackupDatabases;
use App\Models\Site\Site;
use App\Http\Controllers\Controller;

class SiteSchemaBackupsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param  int $siteId
     * @return \Illuminate\Http\Response
     */
    public function index($siteId)
    {
        return response()->json(
            Site::with('servers.backups')->findOrFail($siteId)
        );
    }

    /**
     * @param $siteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($siteId)
    {
        $site = Site::with('servers')->findOrFail($siteId);

        foreach ($site->servers as $server) {
            dispatch((new BackupDatabases($server))->onQueue(
                config('queue.channels.server_commands')
            ));
        }

        return response()->json('OK');
    }
}
