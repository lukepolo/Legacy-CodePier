<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Http\Controllers\Controller;
use App\Models\UserSshKey;

/**
 * Class UserController.
 */
class UserSshKeyController extends Controller
{
    protected $serverService;

    /**
     * UserController constructor.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     */
    public function __construct(ServerService $serverService)
    {
        $this->serverService = $serverService;
    }

    /**
     * Updates a users profile.
     *
     * @return mixed
     */
    public function postMyProfile()
    {
        $user = \Auth::user();

        $user->fill([
            'name'  => \Request::get('name'),
            'email' => \Request::get('email'),
        ]);

        if (\Request::has('password')) {
            $user->password = \Hash::make(\Request::get('password'));
        }

        $user->save();

        return response()->json($user);
    }

    /**
     * Adds a ssh key to the users account.
     */
    public function postAddSshKey()
    {
        $userSshKey = UserSshKey::create([
            'user_id' => \Auth::user()->id,
            'name'    => \Request::get('name'),
            'ssh_key' => trim(\Request::get('ssh_key')),
        ]);

        foreach (\Auth::user()->servers as $server) {
            $this->serverService->installSshKey($server, $userSshKey->ssh_key);
        }

        return back()->with('success', 'You added an ssh key to all your servers');
    }

    /**
     * Removes an ssh from the users account.
     *
     * @param $sshKeyID
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getRemoveSshKey($sshKeyID)
    {
        $sshKey = UserSshKey::findOrFail($sshKeyID);

        foreach (\Auth::user()->servers as $server) {
            $this->serverService->removeSshKey($server, $sshKey->ssh_key);
        }

        $sshKey->delete();

        return back()->with('success', 'You removed an ssh key from all your servers');
    }
}
