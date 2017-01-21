<?php

namespace App\Http\Controllers\Server;

use App\Http\Controllers\Controller;
use App\Jobs\Server\InstallBuoy;
use App\Jobs\Server\RemoveBuoy;
use App\Models\Buoy;
use App\Models\BuoyApp;
use App\Models\Server\Server;
use Illuminate\Http\Request;

class ServerBuoyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $serverId
     * @return \Illuminate\Http\Response
     */
    public function index($serverId)
    {
        return response()->json(Server::findOrFail($serverId)->buoys);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param $serverId
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $serverId)
    {
        $server = Server::findOrFail($serverId);

        $buoyApp = BuoyApp::findOrFail($request->get('buoy_app_id'));

        $localPort = $request->get('local_port');

        if(!$server->bouys
            ->where('local_port', $localPort)
            ->count()
        ) {
            $buoy = Buoy::create([
                'buoy_app_id' => $buoyApp->id,
                'local_port' => $localPort,
                'domain' => $request->get('domain', null),
                'options' => $request->get('options', [])
            ]);

            dispatch(new InstallBuoy($server, $buoyApp));

            return response()->json($buoy);

        }

        return response()->json('Please choose another port ', 400);
    }

    /**
     * Display the specified resource.
     *
     * @param $serverId
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($serverId, $id)
    {
        $server = Server::findOrFail($serverId);

        return response()->json($server->buoys->keyBy('id')->get($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $serverId
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($serverId, $id)
    {
        $server = Server::findOrFail($serverId);

        dispatch(new RemoveBuoy($server, $server->buoys->keyBy('id')->get($id)));

        return response()->json('OK');
    }
}
