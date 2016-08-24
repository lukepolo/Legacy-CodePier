<?php

namespace App\Http\Controllers\Server;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\ServerDaemon;
use Illuminate\Http\Request;

/**
 * Class ServerDaemonController
 *
 * @package App\Http\Controllers\Server\Features
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
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json(Server::findOrFail($request->get('server_id'))->daemons);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $serverId)
    {
        $serverDaemon = ServerDaemon::create([
            'server_id' => $serverId,
            'command' => $request->get('command'),
            'auto_start' => $request->get('auto_start', 0),
            'auto_restart' => $request->get('auto_restart', 0),
            'user' => $request->get('user'),
            'number_of_workers' => $request->get('number_of_workers'),
        ]);

        $this->serverService->installDaemon($serverDaemon);

        return response()->json($serverDaemon);
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
        return response()->json(ServerDaemon::findOrFail($id));
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
        $this->serverService->removeDaemon(ServerDaemon::findOrFail($id));
    }
}
