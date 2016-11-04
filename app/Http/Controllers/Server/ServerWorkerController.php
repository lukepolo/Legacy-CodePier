<?php

namespace App\Http\Controllers\Server;

use App\Http\Controllers\Controller;
use App\Models\Server\Server;
use App\Models\Server\ServerWorker;
use Illuminate\Http\Request;

/**
 * Class ServerWorkerController.
 */
class ServerWorkerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @param $serverId
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $serverId)
    {
        return response()->json(
            Server::findOrFail($serverId)->workers
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @param $serverId
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $serverId)
    {
        return response()->json(
            ServerWorker::create([
                'server_id'         => $serverId,
                'command'           => $request->get('command'),
                'auto_start'        => $request->get('auto_start', 0),
                'auto_restart'      => $request->get('auto_restart', 0),
                'user'              => $request->get('user'),
                'number_of_workers' => $request->get('number_of_workers'),
            ])
        );
    }

    /**
     * Display the specified resource.
     *
     * @param $serverId
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($serverId, $id)
    {
        return response()->json(
            ServerWorker::where('server_id', $serverId)->findOrFail($id)
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $serverId
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($serverId, $id)
    {
        return response()->json(
           ServerWorker::where('server_id', $serverId)->findOrFail($id)->delete()
       );
    }
}
