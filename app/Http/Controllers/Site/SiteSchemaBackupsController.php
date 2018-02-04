<?php

namespace App\Http\Controllers\Site;

use App\Models\Site\Site;
use App\Http\Controllers\Controller;

class SiteSchemaBackupsController extends Controller
{
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
            Site::findOrFail($siteId)->backups->where('type', 'mysql')
        );
    }

    /**
     * @param $siteId
     * @param $backupId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($siteId, $backupId) {
        $site = Site::findOrFail($siteId);

        $backup = $site->backups->where('id', $backupId)->first();

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
