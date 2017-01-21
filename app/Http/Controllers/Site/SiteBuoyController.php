<?php

namespace App\Http\Controllers\Site;

use App\Events\Site\SiteBuoyCreated;
use App\Events\Site\SiteBuoyDeleted;
use App\Http\Controllers\Controller;
use App\Models\Buoy;
use App\Models\BuoyApp;
use App\Models\Site\Site;
use Illuminate\Http\Request;

class SiteBuoyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $siteId
     * @return \Illuminate\Http\Response
     */
    public function index($siteId)
    {
        return response()->json(Site::findOrFail($siteId)->buoys);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param $siteId
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $siteId)
    {
        $site = Site::findOrFail($siteId);

        $buoyApp = BuoyApp::findOrFail($request->get('buoy_app_id'));

        $localPort = $request->get('local_port');

        if(!$site->bouys
            ->where('local_port', $localPort)
            ->count()
        ) {
            $buoy = Buoy::create([
                'buoy_app_id' => $buoyApp->id,
                'local_port' => $localPort,
                'domain' => $request->get('domain', null),
                'options' => $request->get('options', [])
            ]);

            event(new SiteBuoyCreated($site, $buoy));

            return response()->json($buoy);

        }

        return response()->json('Please choose another port ', 400);
    }

    /**
     * Display the specified resource.
     *
     * @param $siteId
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($siteId, $id)
    {
        $site = Site::findOrFail($siteId);

        return response()->json($site->buoys->keyBy('id')->get($id));
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
        $site = Site::findOrFail($siteId);

        event(new SiteBuoyDeleted($site, $site->buoys->keyBy('id')->get($id)));

        return response()->json('OK');
    }
}
