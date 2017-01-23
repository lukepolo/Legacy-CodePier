<?php

namespace App\Http\Controllers\Server;


use App\Models\Buoy;
use App\Models\BuoyApp;
use Illuminate\Http\Request;
use App\Models\Server\Server;
use App\Jobs\Server\Buoys\InstallBuoy;
use App\Jobs\Server\Buoys\RemoveBuoy;
use App\Http\Controllers\Controller;

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

        $localPorts = collect($request->get('ports', []))->map(function ($port) {
            return isset($port['local_port']) ? $port['local_port'] : null;
        })->values()->toArray();

        $options = collect($request->get('options', []))->map(function ($option) {
            return isset($option['value']) ? $option['value'] : null;
        })->toArray();

        if (! $server->buoys
            ->whereIn('local_port', $localPorts)
            ->count()
        ) {
            $buoy = Buoy::create([
                'buoy_app_id' => $buoyApp->id,
                'ports' => $localPorts,
                'options' => $options,
                'domain' => $request->get('domain', null),
                'status' => 'Queued',
            ]);

            dispatch(new InstallBuoy($server, $buoy));

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
