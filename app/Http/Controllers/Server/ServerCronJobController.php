<?php

namespace App\Http\Controllers\Server;

use App\Http\Controllers\Controller;
use App\Models\Server\Server;
use App\Models\Server\ServerCronJob;
use Illuminate\Http\Request;

class ServerCronJobController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $serverId
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $serverId)
    {
        return response()->json(
            Server::with('cronJobs')->findOrFail($serverId)->cronJobs
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param $serverId
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $serverId)
    {
        return response()->json(
            ServerCronJob::create([
                'server_id' => $serverId,
                'job' => $request->get('cron_timing').' '.$request->get('cron'),
                'user' => $request->get('user'),
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
            ServerCronJob::where('server_id', $serverId->findOrFail($id))
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
            ServerCronJob::where('server_id', $serverId)->findorFail($id)->delete()
        );
    }
}
