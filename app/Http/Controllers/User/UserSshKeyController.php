<?php

namespace App\Http\Controllers\User;

use App\Models\SshKey;
use App\Http\Controllers\Controller;
use App\Http\Requests\SshKeyRequest;
use App\Jobs\Server\SshKeys\RemoveServerSshKey;
use App\Jobs\Server\SshKeys\InstallServerSshKey;

class UserSshKeyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(\Auth::user()->sshKeys);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\SshKeyRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SshKeyRequest $request)
    {
        $sshKey = SshKey::create([
            'name' => $request->get('name'),
            'ssh_key' => trim($request->get('ssh_key')),
        ]);

        \Auth::user()->sshKeys()->save($sshKey);

        foreach (\Auth::user()->provisionedServers as $server) {
            dispatch(
                (new InstallServerSshKey($server, $sshKey))->onQueue(env('SERVER_COMMAND_QUEUE'))
            );
        }

        return response()->json($sshKey);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $sshKey = \Auth::user()->sshKeys->keyBy('id')->get($id);

        foreach (\Auth::user()->provisionedServers as $server) {
            dispatch(
                (new RemoveServerSshKey($server, $sshKey))->onQueue(env('SERVER_COMMAND_QUEUE'))
            );
        }

        $sshKey->delete();

        return $this->remoteResponse();
    }
}
