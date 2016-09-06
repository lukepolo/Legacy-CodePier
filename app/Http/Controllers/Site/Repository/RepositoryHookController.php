<?php

namespace App\Http\Controllers\Site\Repository;

use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Http\Controllers\Controller;
use App\Models\Site;
use Illuminate\Http\Request;

/**
 * Class RepositoryHookController.
 */
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
     * @param \Illuminate\Http\Request $request
     * @param $siteId
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $siteId)
    {
        // TODO - i think hooks should have their own model as well
        $site = Site::with('server')->findOrFail($siteId);

        $this->siteService->createDeployHook($site);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $siteId
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($siteId, $id)
    {
        $site = Site::with('server')->findOrFail($id);

        $this->siteService->deleteDeployHook($site);
    }
}
