<?php

namespace App\Http\Controllers\Server;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\ServerSshKey;
use Illuminate\Http\Request;

/**
 * Class ServerSshKeyController.
 */
class ServerSshKeyController extends Controller
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
        return response()->json(ServerSshKey::where('server_id', $serverId)->get());
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
        $server = Server::findOrFail($serverId);

        $serverSshKey = ServerSshKey::create([
            'server_id' => $serverId,
            'name'      => $request->get('name'),
            'ssh_key'   => trim($request->get('ssh_key')),
        ]);

        $this->runOnServer($server, function () use ($server, $serverSshKey) {
            if ($server->ssh_connection) {
                $this->serverService->installSshKey($server, $serverSshKey->ssh_key);
            }
        });

        if (! $this->successful()) {
            $serverSshKey->delete();
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
        return response()->json(ServerSshKey::findOrFail($id));
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
        $serverSshKey = ServerSshKey::findOrFail($id);

        $server = Server::findOrFail($serverId);

        $this->runOnServer($server, function () use ($server, $serverSshKey) {
            if ($server->ssh_connection) {
                $this->serverService->removeSshKey($server, $serverSshKey->ssh_key);
                $serverSshKey->delete();
            }
        });

        return $this->remoteResponse();
    }

    /**
     * Tests a ssh connection to server.
     *
     * @param Request $request
     * @param $serverId
     */
    public function testSSHConnection(Request $request, $serverId)
    {
        $this->serverService->testSSHConnection(Server::findOrFail($serverId));
    }
}
