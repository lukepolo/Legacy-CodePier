<?php

namespace App\Http\Controllers\Server;

use App\Http\Controllers\Controller;
use App\Http\Requests\SshKeyRequest;
use App\Jobs\Server\SshKeys\InstallServerSshKey;
use App\Jobs\Server\SshKeys\RemoveServerSshKey;
use App\Models\Server\Server;
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
     * @param \App\Http\Requests\SshKeyRequest $request
     * @param $serverId
     * @return \Illuminate\Http\Response
     */
    public function store(SshKeyRequest $request, $serverId)
    {
        $server = Server::findOrFail($serverId);

        $sshKey = SshKey::create([
            'name' => $request->get('name'),
            'ssh_key' => trim($request->get('ssh_key')),
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
            (new RemoveServerSshKey($server,
                $server->sshKeys->keyBy('id')->get($id)))->onQueue(env('SERVER_COMMAND_QUEUE'))
        );

        return response()->json('OK');
    }
}
