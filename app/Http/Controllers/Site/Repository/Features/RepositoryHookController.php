<?php

namespace App\Http\Controllers\Site\Repository\Features;

use App\Contracts\Server\Site\SiteServiceContract as SiteService;
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
     */
    public function __construct(SiteService $siteService)
    {
        $this->siteService = $siteService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // TODO - i think hooks should have their own model as well
        $site = Site::with('server')->findOrFail($request->get('site_id'));

        $this->siteService->createDeployHook($site);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $site = Site::with('server')->findOrFail($id);

        $this->siteService->deleteDeployHook($site);
    }
}
