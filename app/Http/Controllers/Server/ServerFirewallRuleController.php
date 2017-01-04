<?php

namespace App\Http\Controllers\Server;

use App\Http\Controllers\Controller;
use App\Http\Requests\Server\ServerFireWallRuleRequest;
use App\Models\FirewallRule;
use App\Models\Server\Server;

class ServerFirewallRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $serverId
     *
     * @return \Illuminate\Http\Response
     */
    public function index($serverId)
    {
        return response()->json(
            Server::findOrFail($serverId)->firewallRules
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ServerFireWallRuleRequest $request
     * @param $serverId
     * @return \Illuminate\Http\Response
     */
    public function store(ServerFireWallRuleRequest $request, $serverId)
    {
        $server = Server::findOrFail($serverId);

        $firewallRule = FirewallRule::create([
            'port' => $request->get('port'),
            'from_ip' => $request->get('from_ip'),
            'description' => $request->get('description'),
        ]);

        $server->firewallRules()->save($firewallRule);

        return response()->json($firewallRule);
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
        return response()->json(
            Server::findOrFail($serverId)->get($id)
        );
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
        return response()->json(
            Server::findOrFail($serverId)->get($id)->delete()
        );
    }
}
