<?php

namespace App\Http\Controllers\Site;

use App\Models\Server\Server;
use App\Models\Site\Site;
use Illuminate\Http\Request;
use App\Models\Site\SiteFile;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\SiteFileRequest;
use App\Contracts\Server\ServerServiceContract as ServerService;

class SiteFileController extends Controller
{
    private $serverService;

    /**
     * ServerController constructor.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     */
    public function __construct(ServerService $serverService)
    {
        $this->serverService = $serverService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param $siteId
     * @return \Illuminate\Http\Response
     */
    public function index($siteId)
    {
        return response()->json(
            SiteFile::where('site_id', $siteId)->get()
        );
    }

    /**
     * Display the specified resource.
     *
     * @param $siteId
     * @param string $fileId
     * @return \Illuminate\Http\Response
     */
    public function show($siteId, $fileId)
    {
        return response()->json(
            SiteFile::where('site_id', $siteId)->findOrFail($fileId)
        );
    }

    /**
     * @param Request $request
     * @param $siteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function find(Request $request, $siteId)
    {
        $file = SiteFile::where('site_id', $siteId)
            ->where('file_path', $request->get('file'))
            ->first();

        if (empty($file)) {
            $servers = Site::with('servers')->findOrFail($siteId)->servers;

            $file = new SiteFile([
                'site_id' => $siteId,
                'file_path' => $request->get('file'),
                'content' => $servers->count() ? $this->serverService->getFile($servers->first(), $request->get('file')) : null,
                'custom' => $request->get('custom', false),
            ]);

            save_without_events($file);
        }

        return response()->json($file);
    }

    /**
     * Update the specified resource in storage.
     * @param SiteFileRequest $request
     * @param $siteId
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(SiteFileRequest $request, $siteId, $id)
    {
        $file = SiteFile::where('site_id', $siteId)->findOrFail($id);

        return response()->json(
            $file->update([
                'content' => $request->get('content'),
            ])
        );
    }

    /**
     * Reloads a file from a server
     *
     * @param $siteId
     * @param $fileId
     * @param $serverId
     * @return \Illuminate\Http\JsonResponse
     */
    public function reloadFile($siteId, $fileId, $serverId) {

        $file = SiteFile::where('site_id', $siteId)->findOrFail($fileId);

        $server = Server::findOrFail($serverId);

        $file->content = $this->serverService->getFile($server, $file->file_path);

//        save_without_events($file);
//
        return response()->json($file);
    }
}
