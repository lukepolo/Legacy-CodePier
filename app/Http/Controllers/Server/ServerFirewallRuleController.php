<?php

namespace App\Http\Controllers\Server;

use App\Models\FirewallRule;
use App\Models\Server\Server;
use App\Http\Controllers\Controller;
use App\Http\Requests\FirewallRuleRequest;
use App\Jobs\Server\FirewallRules\RemoveServerFirewallRule;
use App\Jobs\Server\FirewallRules\InstallServerFirewallRule;

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
     * @param FirewallRuleRequest $request
     * @param $serverId
     * @return \Illuminate\Http\Response
     */
    public function store(FirewallRuleRequest $request, $serverId)
    {
        $server = Server::findOrFail($serverId);

        $port = $request->get('port');
        $fromIp = $request->get('from_ip', null);

        if(!$server->firewallRules
            ->where('port', $port)
            ->where('from_ip', $fromIp)
            ->count()
        ) {
            $firewallRule = FirewallRule::create([
                'port' => $port,
                'from_ip' => $fromIp,
                'description' => $request->get('description'),
            ]);

            $server->firewallRules()->save($firewallRule);

            dispatch(
                (new InstallServerFirewallRule($server, $firewallRule))->onQueue(env('SERVER_COMMAND_QUEUE'))
            );

            return response()->json($firewallRule);
        }

        return response()->json('Firewall Rule Already Exists', 400);

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
        $server = Server::findOrFail($serverId);

        dispatch(
            (new RemoveServerFirewallRule($server,
                $server->firewallRules->keyBy('id')->get($id)))->onQueue(env('SERVER_COMMAND_QUEUE'))
        );

        return response()->json('OK');
    }
}
