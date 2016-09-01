<?php

namespace App\Http\Controllers\Server;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\ServerDaemon;
use App\Services\Systems\SystemService;
use Illuminate\Http\Request;

/**
 * Class ServerDaemonController.
 */
class ServerDaemonController extends Controller
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
        return response()->json(Server::findOrFail($serverId)->daemons);
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

        $serverDaemon = ServerDaemon::create([
            'server_id'         => $serverId,
            'command'           => $request->get('command'),
            'auto_start'        => $request->get('auto_start', 0),
            'auto_restart'      => $request->get('auto_restart', 0),
            'user'              => $request->get('user'),
            'number_of_workers' => $request->get('number_of_workers'),
        ]);

        $this->runOnServer($server, function () use ($server, $serverDaemon) {
            if ($server->ssh_connection) {
                $this->serverService->getService(SystemService::DAEMON, $server)->installDaemon($serverDaemon);
            }
        });

        if (!$this->successful()) {
            $serverDaemon->delete();
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
        return response()->json(ServerDaemon::findOrFail($id));
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

        $serverDaemon = ServerDaemon::findOrFail($id);

        $this->runOnServer($server, function () use ($server, $serverDaemon) {
            if ($server->ssh_connection) {
                $this->serverService->getService(SystemService::DAEMON, $server)->removeDaemon($serverDaemon);
                $serverDaemon->delete();
            }
        });

        return $this->remoteResponse();
    }
}
