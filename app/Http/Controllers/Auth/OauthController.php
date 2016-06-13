<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserServerProvider;
use Socialite;

/**
 * Class OauthController
 * @package app\Http\Controllers\Auth
 */
class OauthController extends Controller
{
    public function postNewProvider()
    {
        return Socialite::driver('digitalocean')->scopes(['read write'])->redirect();
    }

    public function getHandleProviderCallback($service)
    {
        $user = Socialite::driver($service)->user();

        if(\Auth::user()) {

            $userServerProvider = UserServerProvider::firstOrNew([
                'service' => $service,
                'user_id' => \Auth::user()->id,
            ]);

            $userServerProvider->fill([
                'token' => $user->accessTokenResponseBody['access_token'],
                'tokenSecret' => isset($user->tokenSecret) ? $user->tokenSecret : null,
                'provider_id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'nickname' => $user->getNickname(),
                'refresh_token' => $user->accessTokenResponseBody['refresh_token'],
                'expires_in' => $user->accessTokenResponseBody['expires_in']
            ]);

            $userServerProvider->save();

            return back()->with('success', 'You have connected your digital ocean account');
            
        } else {
            if(!User::where('email', $user->getEmail())->first()) {
                dd('This is a login user provider');
            }
        }
    }
}