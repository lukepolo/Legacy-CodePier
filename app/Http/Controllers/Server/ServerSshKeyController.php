<?php

namespace App\Http\Controllers\Server;

use App\Models\SshKey;
use App\Models\Server\Server;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\SshKeyRequest;
use App\Jobs\Server\SshKeys\InstallServerSshKey;

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
     * @param SshKeyRequest $request
     * @param $serverId
     * @return \Illuminate\Http\Response
     */
    public function store(SshKeyRequest $request, $serverId)
    {
        $server = Server::findOrFail($serverId);

        $sshKey = SshKey::create([
            'name'      => $request->get('name'),
            'ssh_key'   => trim($request->get('ssh_key')),
        ]);

        $server->sshKeys()->save($sshKey);

        dispatch(
            (new InstallServerSshKey($server, $sshKey))->onQueue(env('SERVER_COMMAND_QUEUE'))
        );

        return response()->json($sshKey);
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
            (new InstallServerSshKey($server, $server->sshKeys->keyBy('id')->get($id)))->onQueue(env('SERVER_COMMAND_QUEUE'))
        );

        return response()->json('OK');
    }
}
