<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Http\Controllers\Controller;
use App\Models\RepositoryProvider;
use App\Models\ServerProvider;
use App\Models\UserSshKey;

/**
 * Class UserController
 * @package App\Http\Controllers\Auth
 */
class UserController extends Controller
{
    protected $serverService;

    /**
     * UserController constructor.
     * @param \App\Services\Server\ServerService | ServerService $serverService
     */
    public function __construct(ServerService $serverService)
    {
        $this->serverService = $serverService;
    }

    /**
     * Gets the users profile
     * @return mixed
     */
    public function getMyProfile()
    {
        return view('auth.user.profile', [
            'serverProviders' => ServerProvider::all(),
            'repositoryProviders' => RepositoryProvider::all()
        ]);
    }

    /**
     * Updates a users profile
     * @return mixed
     */
    public function postMyProfile()
    {
        $user = \Auth::user();

        $user->fill([
            'name' => \Request::get('name'),
            'email' => \Request::get('email')
        ]);

        if (\Request::has('password')) {
            $user->password = \Hash::make(\Request::get('password'));
        }

        $user->save();

        return back()->with('success', 'Profile Updated');
    }

    /**
     * Adds a ssh key to the users account
     */
    public function postAddSshKey()
    {
        UserSshKey::create([
            'user_id' => \Auth::user()->id,
            'name' => str_replace(' ', '_', \Request::get('name')),
            'ssh_key' => \Request::get('ssh_key')
        ]);

        foreach (\Auth::user()->servers as $server) {
            $this->serverService->installSshKey($server, \Request::get('ssh_key'));
        }

        return back()->with('success', 'You added an ssh key to all your servers');
    }

    /**
     * Removes an ssh from the users account
     * @param $sshKeyID
     * @return
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
