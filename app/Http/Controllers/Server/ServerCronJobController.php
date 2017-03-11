<?php

namespace App\Http\Controllers\Server;

use App\Models\CronJob;
use App\Models\Server\Server;
use App\Http\Controllers\Controller;
use App\Http\Requests\CronJobRequest;
use App\Jobs\Server\CronJobs\RemoveServerCronJob;
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
     * @param CronJobRequest $request
     * @param $serverId
     * @return \Illuminate\Http\Response
     */
    public function store(CronJobRequest $request, $serverId)
    {
        $server = Server::findOrFail($serverId);

        $job = $request->get('job');
        $user = $request->get('user');

        if (! $server->cronJobs
            ->where('job', $job)
            ->where('user', $user)
            ->count()
        ) {
            $cronJob = CronJob::create([
                'job' => $job,
                'user' => $user,
            ]);

            $server->cronJobs()->save($cronJob);

            $this->dispatch(
                (new InstallServerCronJob($server, $cronJob))->onQueue(config('queue.channels.server_commands'))
            );

            return response()->json($cronJob);
        }

        return response()->json('Cron Job Already Exists', 400);
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
            (new RemoveServerCronJob($server, $server->cronJobs->keyBy('id')->get($id)))->onQueue(config('queue.channels.server_commands'))
        );

        return response()->json('OK');
    }
}
