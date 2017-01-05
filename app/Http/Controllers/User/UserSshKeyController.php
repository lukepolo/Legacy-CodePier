<?php

namespace App\Http\Controllers\User;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Http\Controllers\Controller;
use App\Http\Requests\SshKeyRequest;
use App\Models\User\UserSshKey;

class UserSshKeyController extends Controller
{
    private $serverService;

    /**
     * UserSshKeyController constructor.
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
        $userSshKey = UserSshKey::create([
            'user_id' => \Auth::user()->id,
            'name'    => $request->get('name'),
            'ssh_key' => trim($request->get('ssh_key')),
        ]);

        // TODO - this needs to be dispatched
        foreach (\Auth::user()->provisionedServers as $server) {
            $this->runOnServer(function () use ($server, $userSshKey) {
                if ($server->ssh_connection) {
                    $this->serverService->installSshKey($server, $userSshKey->ssh_key);
                }
            });
        }

        return response()->json($userSshKey);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(UserSshKey::findOrFail($id));
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
        $sshKey = UserSshKey::findOrFail($id);

        // TODO - this needs to be a job
        foreach (\Auth::user()->servers as $server) {
            $this->runOnServer(function () use ($server, $sshKey) {
                if ($server->ssh_connection) {
                    $this->serverService->removeSshKey($server, $sshKey->ssh_key);
                }
            });
        }

        $sshKey->delete();

        return $this->remoteResponse();
    }
}
