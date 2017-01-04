<?php

namespace App\Http\Controllers\Server;

use App\Http\Controllers\Controller;
use App\Models\CronJob;
use App\Models\Server\Server;
use App\Http\Requests\Server\ServerCronJobRequest;

class ServerCronJobController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $serverId
     *
     * @return \Illuminate\Http\Response
     */
    public function index($serverId)
    {
        return response()->json(
            Server::findOrFail($serverId)->cronJobs
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ServerCronJobRequest $request
     * @param $serverId
     * @return \Illuminate\Http\Response
     */
    public function store(ServerCronJobRequest $request, $serverId)
    {
        $server = Server::findOrFail($serverId);

        $cronJob = CronJob::create([
            'job' => $request->get('cron_timing').' '.$request->get('cron'),
            'user' => $request->get('user'),
        ]);

        $server->cronJob()->save($cronJob);

        return response()->json($cronJob);
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
            Server::findOrFail($serverId)->cronJobs->get($id)
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
            Server::findOrFail($serverId)->cronJobs->get($id)->delete()
        );
    }
}
