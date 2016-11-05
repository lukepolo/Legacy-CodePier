<?php

namespace App\Http\Controllers\Site;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Http\Controllers\Controller;
use App\Models\Site\Site;
use App\Models\Site\SiteFile;
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
     * @return \Illuminate\Http\Response
     */
    public function index($siteId)
    {
        return response()->json(
            SiteFile::findOrFail($siteId)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @param $siteId
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $siteId)
    {
        return response()->json(
            SiteFile::create([
                'site_id' => $siteId,
                'file_path' => $request->get('file_path'),
                'content' => $request->get('content'),
            ])
        );
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param $siteId
     * @param string $fileId
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $siteId, $fileId)
    {
        return response()->json(
            SiteFile::with('site')->where('site_id', $siteId)->findOrFail($fileId)
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

            $file = SiteFile::create([
                'site_id' => $siteId,
                'file_path' => $request->get('file'),
                'content' => $servers->count() ? $this->serverService->getFile($servers->first(), $request->get('file')) : null,
            ]);
        }

        return response()->json($file);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $siteId, $id)
    {
        $file = SiteFile::with('site')->where('site_id', $siteId)->findOrFail($id);

        return response()->json(
            $file->update([
                'content' => $request->get('content'),
            ])
        );
    }
}
