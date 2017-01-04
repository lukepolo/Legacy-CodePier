<?php

namespace App\Http\Controllers\Site;

use App\Models\File;
use App\Models\Site\Site;
use Illuminate\Http\Request;
use App\Models\Server\Server;
use App\Events\Site\SiteFileUpdated;
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
            Site::findOrFail($siteId)->files
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
            Site::findOrFail($siteId)->files->get($fileId)
        );
    }

    /**
     * @param Request $request
     * @param $siteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function find(Request $request, $siteId)
    {
        $site = Site::findOrFail($siteId);

        $file = $site->files
            ->where('file_path', $request->get('file'))
            ->first();

        if (empty($file)) {
            $servers = Site::with('servers')->findOrFail($siteId)->servers;

            $file = File::create([
                'file_path' => $request->get('file'),
                'content' => $servers->count() ? $this->serverService->getFile($servers->first(), $request->get('file')) : null,
                'custom' => $request->get('custom', false),
            ]);

            $site->files()->save($file);
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
        $file = Site::findOrFail($siteId)->get($id);

        $file->update([
            'content' => $request->get('content'),
        ]);

        event(new SiteFileUpdated($file));

        return response()->json($file);
    }

    /**
     * Reloads a file from a server.
     *
     * @param $siteId
     * @param $fileId
     * @param $serverId
     * @return \Illuminate\Http\JsonResponse
     */
    public function reloadFile($siteId, $fileId, $serverId)
    {
        $file = $file = Site::findOrFail($siteId)->get($fileId);

        $server = Server::findOrFail($serverId);

        $file->content = $this->serverService->getFile($server, $file->file_path);

        save_without_events($file);

        return response()->json($file);
    }
}
