<?php

namespace App\Http\Controllers\Server;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Http\Controllers\Controller;
use App\Models\Server\Server;
use App\Models\Server\ServerWorker;
use App\Services\Systems\SystemService;
use Illuminate\Http\Request;

/**
 * Class ServerWorkerController.
 */
class ServerWorkerController extends Controller
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
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $serverId)
    {
        return response()->json(Server::findOrFail($serverId)->workers);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $serverId)
    {
        $server = Server::FindOrFail($serverId);

        $serverWorker = ServerWorker::create([
            'server_id'         => $serverId,
            'command'           => $request->get('command'),
            'auto_start'        => $request->get('auto_start', 0),
            'auto_restart'      => $request->get('auto_restart', 0),
            'user'              => $request->get('user'),
            'number_of_workers' => $request->get('number_of_workers'),
        ]);

        $this->runOnServer(function () use ($server, $serverWorker) {
            $this->serverService->getService(SystemService::WORKERS, $server)->addWorker($serverWorker);
        });

        if (! $this->successful()) {
            $serverWorker->delete();
        }

        return $this->remoteResponse();
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
        return response()->json(ServerWorker::findOrFail($id));
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
        $server = Server::FindOrFail($serverId);

        $serverWorker = ServerWorker::findOrFail($id);

        $this->runOnServer(function () use ($server, $serverWorker) {
            $this->serverService->getService(SystemService::WORKERS, $server)->removeWorker($serverWorker);
            $serverWorker->delete();
        });

        return $this->remoteResponse();
    }
}
