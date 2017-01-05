<?php

namespace App\Http\Controllers\Server;

use App\Models\CronJob;
use App\Models\Server\Server;
use App\Http\Controllers\Controller;
use App\Jobs\Server\CronJobs\RemoveServerCronJob;
use App\Http\Requests\Server\ServerCronJobRequest;
use App\Jobs\Server\CronJobs\InstallServerCronJob;

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

        $this->dispatch(
            (new InstallServerCronJob($server, $cronJob))->onQueue(env('SERVER_COMMAND_QUEUE'))
        );

        return response()->json($cronJob);
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
        $server = Server::findOrFail($serverId);

        $this->dispatch(
            (new RemoveServerCronJob($server, $server->cronJobs->get($id)))->onQueue(env('SERVER_COMMAND_QUEUE'))
        );

        return response()->json('OK');
    }
}
