<?php

namespace App\Http\Controllers\Server;

use App\Models\Server\Server;
use App\Http\Controllers\Controller;
use App\Http\Requests\Server\ServerSshKeyRequest;
use App\Models\SshKey;

class ServerSshKeyController extends Controller
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
            Server::findOrFail($serverId)->sshKeys
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ServerSshKeyRequest $request
     * @param $serverId
     * @return \Illuminate\Http\Response
     */
    public function store(ServerSshKeyRequest $request, $serverId)
    {
        $server = Server::findOrFail($serverId);

        $sshKey = SshKey::create([
            'name'      => $request->get('name'),
            'ssh_key'   => trim($request->get('ssh_key')),
        ]);

        $server->sshKeys()->save($sshKey);

        return response()->json($sshKey);
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
