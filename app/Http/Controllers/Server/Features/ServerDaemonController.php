<?php

namespace App\Http\Controllers\Server\Features;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\ServerDaemon;
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
    public function index(Request $request)
    {
        return response()->json(Server::findOrFail($request->get('server_id'))->daemons);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $server = Server::findOrFail($request->get('server_id'));

        $serverDaemon = ServerDaemon::create([
            'server_id'         => $server->id,
            'command'           => $request->get('command'),
            'auto_start'        => $request->get('auto_start', 0),
            'auto_restart'      => $request->get('auto_restart', 0),
            'user'              => $request->get('user'),
            'number_of_workers' => $request->get('number_of_workers'),
        ]);

        $this->serverService->installDaemon($serverDaemon);

        return response()->json($serverDaemon);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(ServerDaemon::findOrFail($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->serverService->removeDaemon(ServerDaemon::findOrFail($id));
    }
}
