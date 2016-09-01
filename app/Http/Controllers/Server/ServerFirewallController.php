<?php

namespace App\Http\Controllers\Server;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\ServerFirewallRule;
use App\Services\Systems\SystemService;
use Illuminate\Http\Request;

/**
 * Class ServerFirewallController.
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
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $serverId)
    {
        return response()->json(ServerFirewallRule::where('server_id', $serverId)->get());
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
        $serverFirewallRule = ServerFirewallRule::create([
            'description' => $request->get('description'),
            'server_id'   => $serverId,
            'port'        => $request->get('port'),
            'from_ip'     => $request->get('from_ip'),
        ]);

        $server = Server::findOrFail($serverId);

        $this->runOnServer($server, function () use ($server, $serverFirewallRule) {
            if ($server->ssh_connection) {
                $this->serverService->getService(SystemService::FIREWALL, $server)->addFirewallRule($serverFirewallRule);
            }
        });

        if (!$this->successful()) {
            $serverFirewallRule->delete();
        }

        return $this->remoteResponse();
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
        return response()->json(ServerFirewallRule::findOrFail($id));
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
        $serverFirewallRule = ServerFirewallRule::findOrFail($id);

        $server = Server::findOrFail($serverId);

        $this->runOnServer($server, function () use ($server, $serverFirewallRule) {
            if ($server->ssh_connection) {
                $this->serverService->getService(SystemService::FIREWALL, $server)->removeFirewallRule($serverFirewallRule);
                $serverFirewallRule->delete();
            }
        });

        return $this->remoteResponse();
    }
}
