<?php

namespace App\Http\Controllers\Server;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\ServerCronJob;
use Illuminate\Http\Request;

/**
 * Class ServerCronJobController
 *
 * @package App\Http\Controllers\Server\Features
 */
class ServerCronJobController extends Controller
{
    private $serverService;

    /**
     * ServerController constructor.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     */
    public function __construct(ServerService $serverService)
    {
        $this->serverService = $serverService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $serverId
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $serverId)
    {
        return response()->json(Server::with('cronJobs')->findOrFail($serverId)->cronJobs);
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
        $serverCronJob = ServerCronJob::firstOrCreate([
            'server_id' => $serverId,
            'job' =>  $request->get('cron_timing') . ' ' .$request->get('cron'),
            'user' => $request->get('user')
        ]);

        $server = Server::findOrFail($serverId);

        $this->runOnServer($server, function () use ($server, $serverCronJob) {
            if($server->ssh_connection) {
                $this->serverService->installCron($serverCronJob);
                $serverCronJob->save();
            }
        });

        return $this->remoteResponse();
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
        return response()->json(ServerCronJob::findOrFail($id));
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
        $serverCronJob = ServerCronJob::findorFail($id);

        $server = Server::findOrFail($serverId);

        $this->runOnServer($server, function () use ($server, $serverCronJob) {
            if($server->ssh_connection) {
                $this->serverService->installCron($serverCronJob);
                $serverCronJob->delete();
            }
        });

        return $this->remoteResponse();
    }
}
