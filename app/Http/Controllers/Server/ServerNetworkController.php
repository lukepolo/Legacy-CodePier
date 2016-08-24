<?php

namespace App\Http\Controllers\Server;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\ServerNetworkRule;
use Illuminate\Http\Request;

/**
 * Class ServerNetworkController
 * @package App\Http\Controllers\Server\Features
 */
class ServerNetworkController extends Controller
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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // TODO - come back to
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $server = Server::findOrFail($request->get('server_id'));

        $connectToServers = Server::whereIn('id', \Request::get('servers'))->whereDoesntHave('connectedServers')->get();

        foreach ($connectToServers as $connectToServer) {

            ServerNetworkRule::create([
                'server_id' => $connectToServer->id,
                'connect_to_server_id' => $server->id,
            ]);

            $this->serverService->addServerNetworkRule($connectToServer, $server->ip);
        }

        $serverNetworkRules = ServerNetworkRule::with('server')->where('connect_to_server_id', $server->id)->whereNotIn('server_id', \Request::get('servers', []))->get();

        foreach ($serverNetworkRules as $serverNetworkRule) {
            $this->serverService->removeServerNetworkRule($serverNetworkRule->server, $server->ip);

            $serverNetworkRule->delete();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }
}
