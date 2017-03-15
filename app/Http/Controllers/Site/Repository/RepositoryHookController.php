<?php

namespace App\Http\Controllers\Site\Repository;

use App\Exceptions\DeployHookFailed;
use App\Models\Site\Site;
use App\Http\Controllers\Controller;
use App\Contracts\Site\SiteServiceContract as SiteService;

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
        try {
            return response()->json(
                $this->siteService->createDeployHook(Site::findOrFail($siteId))
            );
        } catch(DeployHookFailed $e) {
            return response()->json($e->getMessage(), 400);
        }
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
            $this->siteService->deleteDeployHook(Site::findOrFail($siteId))
        );
    }
}
