<?php

namespace App\Http\Controllers\Server;

use App\Http\Controllers\Controller;
use App\Http\Requests\Server\ServerNetworkRuleRequest;
use App\Models\Server\Server;
use App\Models\Server\ServerNetworkRule;

class ServerNetworkRuleController extends Controller
{
    /**
     * @param $serverId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($serverId)
    {
        return response()->json(
            ServerNetworkRule::where('server_id', $serverId)->get()
        );
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
     * @param ServerNetworkRuleRequest $request
     * @param $serverId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ServerNetworkRuleRequest $request, $serverId)
    {
        $server = Server::findOrFail($serverId);

        foreach (Server::whereIn('id', $request->get('servers'))->whereDoesntHave('connectedServers')->get() as $connectToServer) {
            ServerNetworkRule::create([
                'server_id'            => $connectToServer->id,
                'connect_to_server_id' => $server->id,
            ]);
        }

        foreach (ServerNetworkRule::with('server')->where('connect_to_server_id', $server->id)->whereNotIn('server_id', $request->get('servers', []))->get() as $serverNetworkRule) {
            $serverNetworkRule->delete();
        }

        return response()->json('OK');
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
