<?php

namespace App\Http\Controllers\Site\Repository;

use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Http\Controllers\Controller;
use App\Models\Site\Site;

class RepositoryHookController extends Controller
{
    private $siteService;

    /**
     * RepositoryHookController constructor.
     * @param \App\Services\Site\SiteService | SiteService $siteService
     */
    public function __construct(SiteService $siteService)
    {
        $this->siteService = $siteService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $siteId
     *
     * @return \Illuminate\Http\Response
     */
    public function store($siteId)
    {
        return response()->json(
            $this->siteService->createDeployHook(Site::with('server')->findOrFail($siteId))
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $siteId
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($siteId)
    {
        return response()->json(
            $this->siteService->deleteDeployHook(Site::with('server')->findOrFail($siteId))
        );
    }
}
