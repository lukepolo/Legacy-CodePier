<?php

namespace App\Http\Controllers\Site;

use App\Models\Site\Site;
use Illuminate\Http\Request;
use App\Models\Server\Server;
use App\Http\Requests\FileRequest;
use App\Events\Site\SiteFileUpdated;
use App\Http\Controllers\Controller;
use App\Contracts\Repositories\FileRepositoryContract as FileRepository;

class SiteFileController extends Controller
{
    private $fileRepository;

    /**
     * ServerController constructor.
     * @param \App\Repositories\FileRepository | FileRepository $fileRepository
     */
    public function __construct(FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
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
            $this->fileRepository->findOnModelById(Site::with('files')->findOrFail($siteId), $fileId)
        );
    }

    /**
     * @param Request $request
     * @param $siteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function find(Request $request, $siteId)
    {
        return response()->json(
            $this->fileRepository->findOrCreateFile(
                Site::findOrFail($siteId),
                $request->get('file'),
                $request->get('custom', false)
            )
        );
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

        $file = $this->fileRepository->update(
            $this->fileRepository->findOnModelById($site, $id),
            $request->get('content')
        );

        event(new SiteFileUpdated($site, $file));

        return response()->json($file);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $siteId
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($siteId, $id)
    {
        return response()->json(
            $this->fileRepository->destroy(
                $this->fileRepository->findOnModelById(Site::with('files')->findOrFail($siteId), $id)
            )
        );
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
        return response()->json(
            $this->fileRepository->reload(
                Site::with('files')
                    ->findOrFail($siteId)
                    ->files
                        ->keyBy('id')
                        ->get($fileId),
                Server::findOrFail($serverId)
            )
        );
    }
}
