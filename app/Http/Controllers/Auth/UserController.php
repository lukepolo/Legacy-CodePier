<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ServerProvider;
use App\Models\UserSshKey;

/**
 * Class UserController
 * @package App\Http\Controllers\Auth
 */
class UserController extends Controller
{
    public function getMyProfile()
    {
        return view('auth.user.profile', [
            'serverProviders' => ServerProvider::all(),
            'repositoryProviders' => \App\Http\Controllers\Auth\OauthController::$repositoryProviders
        ]);
    }

    public function postMyProfile()
    {
        $user = \Auth::user();

        $user->fill([
            'name' => \Request::get('name'),
            'email' => \Request::get('email')
        ]);

        if(\Request::has('password')) {
            $user->password = \Hash::make(\Request::get('password'));
        }

        $user->save();

        return back()->with('success', 'Profile Updated');
    }

    /**
     * Installs a SSH key onto a server
     */
    public function postAddSshKey()
    {
        UserSshKey::create([
            'user_id' => \Auth::user()->id,
            'name' => str_replace(' ', '_', \Request::get('name')),
            'ssh_key' => \Request::get('ssh_key')
        ]);

        return back()->with('success', 'You added an ssh key');
    }

    /**
     * Removes a SSH key on a server
     *
     * @param $sshKeyID
     * @return
     */
    public function getRemoveSshKey($sshKeyID)
    {
        UserSshKey::findOrFail($sshKeyID)->delete();

        return back()->with('success', 'You removed an ssh key');
    }
}
