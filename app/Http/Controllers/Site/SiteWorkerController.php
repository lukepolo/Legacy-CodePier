<?php

namespace App\Http\Controllers\Site;

use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\SiteDaemon;
use Illuminate\Http\Request;

/**
 * Class SiteWorkerController.
 */
class SiteWorkerController extends Controller
{
    private $siteService;

    /**
     * RepositoryHookController constructor.
     *
     * @param \App\Services\Site\SiteService |SiteService $siteService
     */
    public function __construct(SiteService $siteService)
    {
        $this->siteService = $siteService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $siteId
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $siteId)
    {
        return response()->json(SiteDaemon::where('site_id', $siteId)->get());
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
        $site = Site::findOrFail($siteId);

        // MOVE TO LARAVEL SPECIFIC AREA , front end can decide show / hide certain framework features that you can do from the site vue!
        $serverDaemon = SiteDaemon::create([
            'site_id'           => $site->id,
            'command'           => '/home/codepier/'.$site->domain.($site->zerotime_deployment ? '/current' : null).'/artisan queue:work '.$request->get('connection').' --queue='.$request->get('queue_channel').' --timeout='.$request->get('timeout').' --sleep='.$request->get('sleep').' --tries='.$request->get('tries').' '.($request->get('daemon') ? '--daemon' : null),
            'auto_start'        => true,
            'auto_restart'      => true,
            'user'              => 'codepier',
            'number_of_workers' => $request->get('number_of_workers'),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param $siteId
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($siteId, $id)
    {
        return response()->json(SiteDaemon::findOrFail($id));
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
        SiteDaemon::findOrFail($id)->delete();
    }
}
