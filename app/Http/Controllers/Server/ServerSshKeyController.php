<?php

namespace App\Http\Controllers\Server;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Http\Controllers\Controller;
use App\Jobs\Server\InstallServerSshKey;
use App\Jobs\Server\RemoveServerSshKey;
use App\Models\Server\ServerSshKey;
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
        $serverSshKey = ServerSshKey::create([
            'server_id' => $serverId,
            'name'      => $request->get('name'),
            'ssh_key'   => trim($request->get('ssh_key')),
        ]);

        return $this->dispatchNow(new InstallServerSshKey($serverSshKey));
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
        return response()->json(ServerSshKey::where('server_id', $serverId)->findOrFail($id));
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
        return $this->dispatchNow(new RemoveServerSshKey(ServerSshKey::where('server_id', $serverId)->findOrFail($id)));
    }
}
