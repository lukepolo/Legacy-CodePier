<?php

namespace App\Http\Controllers\Server;

use App\Http\Controllers\Controller;
use App\Models\Server\ServerNetworkRule;
use Illuminate\Http\Request;

/**
 * Class ServerNetworkRuleController.
 */
class ServerNetworkRuleController extends Controller
{
    /**
     * @param Request $request
     * @param $serverId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, $serverId)
    {
        return response()->json(
            ServerNetworkRule::where('server_id', $serverId)->get()
        );
    }

    /**
     * @param Request $request
     * @param $serverId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $serverId)
    {
//        dispatch(new ServerNetworkRule());
    }

    /**
     * @param $serverId
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($serverId, $id)
    {
        return response()->json(
            ServerNetworkRule::where('server_id', $serverId)->findOrFail($id)
        );
    }

    /**
     * @param Request $request
     */
    public function update(Request $request)
    {
//        $server = Server::findOrFail($request->get('server_id'));
//
//        $connectToServers = Server::whereIn('id', \Request::get('servers'))->whereDoesntHave('connectedServers')->get();
//
//        foreach ($connectToServers as $connectToServer) {
//            ServerNetworkRule::create([
//                'server_id'            => $connectToServer->id,
//                'connect_to_server_id' => $server->id,
//            ]);
//
//            $this->serverService->addServerNetworkRule($connectToServer, $server->ip);
//        }
//
//        $serverNetworkRules = ServerNetworkRule::with('server')->where('connect_to_server_id', $server->id)->whereNotIn('server_id', \Request::get('servers', []))->get();
//
//        foreach ($serverNetworkRules as $serverNetworkRule) {
//            $this->serverService->removeServerNetworkRule($serverNetworkRule->server, $server->ip);
//
//            $serverNetworkRule->delete();
//        }
//        dispatch(new ServerNetworkRule());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $serverId
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($serverId, $id)
    {
        return response()->json(
            ServerNetworkRule::where('server_id', $serverId)->findOrFail($id)->delete()
        );
    }
}
