<?php

namespace App\Http\Controllers\Server;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Http\Controllers\Controller;
use App\Jobs\Server\InstallServerCronJob;
use App\Jobs\Server\RemoveServerCronJob;
use App\Models\Server\Server;
use App\Models\Server\ServerCronJob;
use Illuminate\Http\Request;

/**
 * Class ServerCronJobController.
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
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $serverId)
    {
        return response()->json(Server::with('cronJobs')->findOrFail($serverId)->cronJobs);
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
        $serverCronJob = ServerCronJob::create([
            'server_id' => $serverId,
            'job' => $request->get('cron_timing') . ' ' . $request->get('cron'),
            'user' => $request->get('user'),
        ]);

        return $this->dispatchNow(new InstallServerCronJob($serverCronJob));
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
        return response()->json(ServerCronJob::where('server_id', $serverId->findOrFail($id)));
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
        return $this->dispatchNow(new RemoveServerCronJob(ServerCronJob::where('server_id',
            $serverId)->findorFail($id)));

    }
}
