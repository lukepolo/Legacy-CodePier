<?php

namespace App\Http\Controllers\User;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Http\Controllers\Controller;
use App\Models\UserSshKey;
use Illuminate\Http\Request;

/**
 * Class UserSshKeyController.
 */
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
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userSshKey = UserSshKey::create([
            'user_id' => \Auth::user()->id,
            'name'    => \Request::get('name'),
            'ssh_key' => trim(\Request::get('ssh_key')),
        ]);

        foreach (\Auth::user()->provisionedServers as $server) {
            $this->runOnServer($server, function () use ($server, $userSshKey) {
                if ($server->ssh_connection) {
                    $this->serverService->installSshKey($server, $userSshKey->ssh_key);
                }
            });
        }

        return $this->remoteResponse();
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
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sshKey = UserSshKey::findOrFail($id);

        foreach (\Auth::user()->servers as $server) {
            $this->runOnServer($server, function () use ($server, $sshKey) {
                if ($server->ssh_connection) {
                    $this->serverService->removeSshKey($server, $sshKey->ssh_key);
                }
            });
        }

        $sshKey->delete();

        return $this->remoteResponse();
    }
}
