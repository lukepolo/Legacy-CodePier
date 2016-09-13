<?php

namespace App\Http\Controllers\Site\Features;

use App\Contracts\Server\Site\SiteServiceContract as SiteService;
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
     * @param \App\Services\Server\Site\SiteService |SiteService $siteService
     */
    public function __construct(SiteService $siteService)
    {
        $this->siteService = $siteService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json(SiteDaemon::where('site_id', $request->get('site'))->get());
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
        $site = Site::findOrFail($request->get('site'));

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
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(SiteDaemon::findOrFail($id));
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
        SiteDaemon::findOrFail($id)->delete();
    }
}
