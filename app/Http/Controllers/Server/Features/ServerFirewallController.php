<?php

namespace App\Http\Controllers\Server\Features;

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
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json(ServerFirewallRule::where('server_id', $request->get('server_id'))->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $serverFirewallRule = ServerFirewallRule::create([
            'description' => $request->get('description'),
            'server_id' => $request->get('server_id'),
            'port' => $request->get('port'),
            'from_ip' => $request->get('from_ip')
        ]);

        $this->serverService->addFirewallRule($serverFirewallRule);

        return response()->json($serverFirewallRule);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(ServerFirewallRule::findOrFail($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $firewallRule = ServerFirewallRule::findOrFail($id);
        $this->serverService->removeFirewallRule($firewallRule);

        $firewallRule->delete();
    }
}
