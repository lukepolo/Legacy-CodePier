<?php

namespace App\Http\Controllers\Site;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Events\Site\SiteFileUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\FileRequest;
use App\Models\File;
use App\Models\Server\Server;
use App\Models\Site\Site;
use Illuminate\Http\Request;

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
            $servers = Site::with('servers.files')->findOrFail($siteId)->servers;

            foreach ($servers as $server) {
                $file = $server->files->first(function ($file) use ($request) {
                    return $file->file_path == $request->get('file');
                });
                if (!empty($file)) {
                    break;
                }
            }

            if (empty($file)) {
                $file = File::create([
                    'file_path' => $request->get('file'),
                    'content' => $servers->count() ? $this->serverService->getFile($servers->first(),
                        $request->get('file')) : null,
                    'custom' => $request->get('custom', false),
                ]);
            }

            $site->files()->save($file);
        }

        return response()->json($file);
    }

    /**
     * Update the specified resource in storage.
     * @param FileRequest $request
     * @param $siteId
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(FileRequest $request, $siteId, $id)
    {
        $site = Site::with('files')->findOrFail($siteId);

        $file = $site->files->keyBy('id')->get($id);

        $file->update([
            'content' => $request->get('content'),
        ]);

        event(new SiteFileUpdated($site, $file));

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
        $site = Site::with('files')->findOrFail($siteId);

        $file = $site->files->keyBy('id')->get($fileId);

        $file->update([
            'content' => $this->serverService->getFile(Server::findOrFail($serverId), $file->file_path),
        ]);

        return response()->json($file);
    }
}
