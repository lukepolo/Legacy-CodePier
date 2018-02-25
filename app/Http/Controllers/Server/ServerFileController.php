<?php

namespace App\Http\Controllers\Server;

use App\Models\Server\Server;
use App\Http\Requests\FileRequest;
use App\Http\Controllers\Controller;
use App\Jobs\Server\UpdateServerFile;
use App\Http\Requests\FindFileRequest;
use App\Contracts\Repositories\FileRepositoryContract as FileRepository;

class ServerFileController extends Controller
{
    private $fileRepository;

    /**
     * ServerController constructor.
     *
     * @param \App\Repositories\FileRepository | FileRepository $fileRepository
     */
    public function __construct(FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param $serverId
     *
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
     *
     * @return \Illuminate\Http\Response
     */
    public function show($serverId, $fileId)
    {
        return response()->json(
            $this->fileRepository->findOnModelById(Server::with('files')->findOrFail($serverId), $fileId)
        );
    }

    /**
     * @param FindFileRequest $request
     * @param $serverId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function find(FindFileRequest $request, $serverId)
    {
        $server = Server::findOrFail($serverId);

        if ($server->progress < 100) {
            return response()->json('This server is not fully provisioned, please wait', 400);
        }

        $file = $this->fileRepository->findOrCreateFile(
            $server,
            $request->get('file'),
            $request->get('custom', false)
        );

        return response()->json(
            $this->fileRepository->reload($file, $server)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param FileRequest $request
     * @param $serverId
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(FileRequest $request, $serverId, $id)
    {
        $server = Server::with('files')->findOrFail($serverId);

        $file = $this->fileRepository->update(
            $this->fileRepository->findOnModelById($server, $id),
            $request->get('content')
        );

        dispatch(
            new UpdateServerFile($server, $file)
        );

        return response()->json($file);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $serverId
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($serverId, $id)
    {
        return response()->json(
            $this->fileRepository->destroy(
                $this->fileRepository->findOnModelById(Server::with('files')->findOrFail($serverId), $id)
            )
        );
    }

    /**
     * Reloads a file from a server.
     *
     * @param $serverId
     * @param $fileId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function reloadFile($serverId, $fileId)
    {
        return response()->json(
            $this->fileRepository->reload(
                Server::with('files')
                    ->findOrFail($serverId)
                    ->files
                    ->keyBy('id')
                    ->get($fileId),
                Server::findOrFail($serverId)
            )
        );
    }
}
