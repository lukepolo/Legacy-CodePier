<?php

namespace App\Http\Controllers\Site;

use App\Models\Daemon;
use App\Models\Site\Site;
use App\Http\Controllers\Controller;
use App\Http\Requests\DaemonRequest;
use App\Events\Site\SiteDaemonCreated;
use App\Events\Site\SiteDaemonDeleted;
use App\Http\Requests\DaemonUpdatedRequest;
use App\Events\Site\FixSiteServerConfigurations;

class SiteDaemonsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $siteId
     *
     * @return \Illuminate\Http\Response
     */
    public function index($siteId)
    {
        return response()->json(
            Site::findOrFail($siteId)->daemons
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DaemonRequest $request
     * @param $siteId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(DaemonRequest $request, $siteId)
    {
        /** @var Site $site */
        $site = Site::findOrFail($siteId);

        $daemon = Daemon::create([
            'user' => $request->get('user'),
            'command' => $request->get('command'),
            'servers' => $request->get('servers', []),
            'server_types' => $request->get('server_types', []),
        ]);

        $site->daemons()->save($daemon);

        event(new SiteDaemonCreated($site, $daemon));

        return response()->json($daemon);
    }

    /**
     * @param $request
     * @param $siteId
     * @param $cronJobId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(DaemonUpdatedRequest $request, $siteId, $cronJobId)
    {
        /** @var Site $site */
        $site = Site::with('cronJobs')->findOrFail($siteId);

        $daemon = Daemon::findOrFail($cronJobId);

        $daemon->update([
            'servers' => $request->get('servers', []),
            'server_types' => $request->get('server_types', []),
        ]);

        event(new FixSiteServerConfigurations($site));

        return response()->json($daemon);
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
        $site = Site::with('daemons')->findOrFail($siteId);

        event(new SiteDaemonDeleted($site, $site->daemons->keyBy('id')->get($id)));

        return response()->json('OK');
    }
}
