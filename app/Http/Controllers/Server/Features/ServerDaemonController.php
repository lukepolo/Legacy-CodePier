<?php

namespace App\Http\Controllers\Server\Features;

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
        return response()->json(Server::findOrFail($request->get('server_id')->daemons));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->serverService->installDaemon(
            Server::findOrFail($request->get('server_id')),
            $request->get('command'),
            $request->get('auto_start'),
            $request->get('auto_restart'),
            $request->get('user'),
            $request->get('number_of_workers')
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(ServerDaemon::findOrFail($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->serverService->removeDaemon(ServerDaemon::findOrFail($id));
    }
}
