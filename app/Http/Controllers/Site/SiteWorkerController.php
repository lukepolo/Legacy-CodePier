<?php

namespace App\Http\Controllers\Site;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\SiteWorker;
use App\Services\Systems\SystemService;
use Illuminate\Http\Request;

/**
 * Class SiteWorkerController.
 */
class SiteWorkerController extends Controller
{
    private $siteService;
    private $serverService;

    /**
     * RepositoryHookController constructor.
     *
     * @param \App\Services\Site\SiteService |SiteService $siteService
     * @param ServerService $serverService
     */
    public function __construct(SiteService $siteService, ServerService $serverService)
    {
        $this->serverService = $serverService;
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
        return response()->json(SiteWorker::where('site_id', $siteId)->get());
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

        $siteWorker = SiteWorker::create([
            'site_id' => $site->id,
            'command' => '/home/codepier/'.$site->domain.($site->zerotime_deployment ? '/current' : null).'/artisan queue:work '.$request->get('connection').' --queue='.$request->get('queue_channel').' --timeout='.$request->get('timeout').' --sleep='.$request->get('sleep').' --tries='.$request->get('tries').' '.($request->get('daemon') ? '--daemon' : null),
            'auto_start' => true,
            'auto_restart' => true,
            'user' => 'codepier',
            'number_of_workers' => $request->get('number_of_workers'),
        ]);

        // TODO - this has to be changed

        dd('SITE WORKER NEEDS TOB E FIXED');
//        foreach ($site->servers as $server) {
//            $this->runOnServer($server, function () use ($server, $serverDaemon) {
//                $this->serverService->getService(SystemService::DAEMON, $server)->addSiteDaemon($serverDaemon);
//            });
//        }

        return $this->remoteResponse();
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
        return response()->json(SiteWorker::findOrFail($id));
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
        $site = Site::findOrFail($siteId);
        $siteWorker = SiteWorker::findOrFail($id)->delete();

        // TODO - needs to be fixed
        dd('SITE WORKER DELETE');
//        foreach ($site->servers as $server) {
//            $this->runOnServer($server, function () use ($server, $siteDaemon) {
//                $this->serverService->getService(SystemService::DAEMON, $server)->removeSiteDaemon($siteDaemon);
//            });
//
//            if (! $this->successful()) {
//                $siteDaemon->delete();
//            }
//        }

        return $this->remoteResponse();
    }
}
