<?php

namespace App\Http\Controllers\Server;

use App\Http\Controllers\Controller;
use App\Models\Server\ServerFirewallRule;
use Illuminate\Http\Request;

/**
 * Class ServerFirewallRuleController.
 */
class ServerFirewallRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param $serverId
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $serverId)
    {
        return response()->json(
            ServerFirewallRule::where('server_id', $serverId)->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param $serverId
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $serverId)
    {
        return response()->json(
            ServerFirewallRule::create([
                'description' => $request->get('description'),
                'server_id' => $serverId,
                'port' => $request->get('port'),
                'from_ip' => $request->get('from_ip'),
            ])
        );
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
            ServerFirewallRule::where('server_id', $serverId)->findOrFail($id)
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
            ServerFirewallRule::where('server_id', $serverId)->findOrFail($id)->delete()
        );
    }
}
