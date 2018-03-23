<?php

namespace App\Http\Controllers\Server;

use Illuminate\Http\Request;
use App\Models\Server\Server;
use App\Http\Controllers\Controller;
use App\Services\Server\ServerFeatureService;

class ServerSchemaBackupsController extends Controller
{
    private $serverFeatureService;

    /**
     * ServerSchemaBackupsController constructor.
     * @param ServerFeatureService $serverFeatureService
     */
    public function __construct(ServerFeatureService $serverFeatureService)
    {
        $this->serverFeatureService = $serverFeatureService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param $siteId
     *
     * @return \Illuminate\Http\Response
     */
    public function index($siteId)
    {
        return response()->json(
            Server::findOrFail($siteId)->backups
        );
    }

    public function store(Request $request, $serverId)
    {
        $server = Server::findOrFail($serverId);

        $this->serverFeatureService->installFeature($server, 'MonitoringService', 'SchemaBackupScript');

        $server->update([
            'backups_enabled' => $request->get('enabled', false)
        ]);

        return response()->json($server);
    }

    /**
     * @param $serverId
     * @param $backupId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($serverId, $backupId)
    {
        $server = Server::with('backups')->findOrFail($serverId);

        $backup = $server->backups->where('id', $backupId)->first();

        $s3 = \Storage::disk('do-spaces');
        $client = $s3->getDriver()->getAdapter()->getClient();

        $command = $client->getCommand('GetObject', [
            'Bucket' => config('filesystems.disks.do-spaces.bucket'),
            'Key' => $backup->name,
        ]);

        $request = $client->createPresignedRequest($command, '+20 minutes');

        $presignedUrl = (string) $request->getUri();

        return response()->json($presignedUrl);
    }
}
