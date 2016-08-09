<?php

namespace App\Http\Controllers\Server\Features;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\ServerSshKey;
use Illuminate\Http\Request;

/**
 * Class ServerSshKeyController
 *
 * @package App\Http\Controllers\Server\Features
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
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json(ServerSshKey::where('server_id', $request->get('server_id'))->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $server = Server::findOrFail($request->get('server_id'));

        $serverSshKey = ServerSshKey::create([
            'server_id' => $server->id,
            'name' => $request->get('name'),
            'ssh_key' => trim($request->get('ssh_key'))
        ]);

        $this->serverService->installSshKey($server, $serverSshKey->ssh_key);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(ServerSshKey::findOrFail($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $serverSshKey = ServerSshKey::findOrFail($id);

        $this->serverService->removeSshKey($serverSshKey);

        $serverSshKey->delete();

    }

    /**
     * Tests a ssh connection to server
     *
     * @param Request $request
     */
    public function testSSHConnection(Request $request)
    {
        $this->serverService->testSSHConnection(Server::findOrFail($request->get('server_id')));
    }
}
