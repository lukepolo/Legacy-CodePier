<?php

namespace App\Http\Controllers\Server;

use App\Models\File;
use Illuminate\Http\Request;
use App\Models\Server\Server;
use App\Http\Requests\FileRequest;
use App\Http\Controllers\Controller;
use App\Jobs\Server\UpdateServerFile;
use App\Contracts\Server\ServerServiceContract as ServerService;

class ServerFileController extends Controller
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
     * @param $serverId
     * @return \Illuminate\Http\Response
     */
    public function index($serverId)
    {
        return response()->json(
            Server::findOrFail($serverId)->files
        );
    }

    /**
     * Display the specified resource.
     *
     * @param $serverId
     * @param string $fileId
     * @return \Illuminate\Http\Response
     */
    public function show($serverId, $fileId)
    {
        return response()->json(
            Server::findOrFail($serverId)->files->get($fileId)
        );
    }

    /**
     * @param Request $request
     * @param $serverId
     * @return \Illuminate\Http\JsonResponse
     */
    public function find(Request $request, $serverId)
    {
        $server = Server::findOrFail($serverId);

        $file = $server->files
            ->where('file_path', $request->get('file'))
            ->first();

        if (empty($file)) {
            $file = File::create([
                'file_path' => $request->get('file'),
                'content' => $this->serverService->getFile($server, $request->get('file')),
                'custom' => $request->get('custom', false),
            ]);

            $server->files()->save($file);
        }

        return response()->json($file);
    }

    /**
     * Update the specified resource in storage.
     * @param FileRequest $request
     * @param $serverId
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(FileRequest $request, $serverId, $id)
    {
        $server = Server::with('files')->findOrFail($serverId);

        $file = $server->files->keyBy('id')->get($id);

        $file->update([
            'content' => $request->get('content'),
        ]);

        dispatch(new UpdateServerFile($server, $file));

        return response()->json($file);
    }

    /**
     * Reloads a file from a server.
     *
     * @param $serverId
     * @param $fileId
     * @param $serverId
     * @return \Illuminate\Http\JsonResponse
     */
    public function reloadFile($serverId, $fileId)
    {
        $server = Server::with('files')->findOrFail($serverId);

        $file = $server->files->keyBy('id')->get($fileId);

        $file->update([
            'content' => $this->serverService->getFile($server, $file->file_path),
        ]);

        return response()->json($file);
    }
}
