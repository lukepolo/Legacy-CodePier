<?php

namespace App\Http\Controllers\Server;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\ServerFirewallRule;
use Illuminate\Http\Request;

/**
 * Class ServerFirewallController
 *
 * @package App\Http\Controllers\Server\Features
 */
class ServerFirewallController extends Controller
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
     * @param $serverId
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $serverId)
    {
        return response()->json(ServerFirewallRule::where('server_id', $serverId)->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param $serverId
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $serverId)
    {
        $serverFirewallRule = ServerFirewallRule::create([
            'description' => $request->get('description'),
            'server_id' => $serverId,
            'port' => $request->get('port'),
            'from_ip' => $request->get('from_ip')
        ]);

        $this->serverService->addFirewallRule($serverFirewallRule);

        return response()->json($serverFirewallRule);
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
        return response()->json(ServerFirewallRule::findOrFail($id));
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
        $firewallRule = ServerFirewallRule::findOrFail($id);
        $this->serverService->removeFirewallRule($firewallRule);

        $firewallRule->delete();
    }
}
