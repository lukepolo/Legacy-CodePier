<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProvider;
use App\Models\UserRepositoryProvider;
use App\Models\UserServerProvider;
use Socialite;

/**
 * Class OauthController
 * @package app\Http\Controllers\Auth
 */
class OauthController extends Controller
{
    public static $loginProviders = [
        'github'
    ];

    public static $serverProviders = [
        'digitalocean'
    ];

    public static $repositoryProviders = [
        'github'
    ];

    public function postNewProvider($provider)
    {
        switch($provider) {
            case 'github' :
                $scopes = 'write:public_key admin:repo_hook repo';
                break;
            case 'digitalocean':
                $scopes = 'read write';
                break;
        }
        return Socialite::driver($provider)->scopes([$scopes])->redirect();
    }

    public function getHandleProviderCallback($provider)
    {
        $user = Socialite::driver($provider)->user();

        if(\Auth::user()) {

            if(in_array($provider, static::$repositoryProviders)) {
                $serverProvider = UserRepositoryProvider::firstOrNew([
                    'service' => $provider,
                    'user_id' => \Auth::user()->id,
                ]);

                $token = $user->token;
                $refreshToken = $user->refreshToken;
                $expiresIn = $user->expiresIn;

            } else {
                $serverProvider = UserServerProvider::firstOrNew([
                    'service' => $provider,
                    'user_id' => \Auth::user()->id,
                ]);

                $token = $user->accessTokenResponseBody['access_token'];
                $refreshToken = $user->accessTokenResponseBody['refresh_token'];
                $expiresIn = $user->accessTokenResponseBody['expires_in'];
            }

            $serverProvider->fill([
                'token' => $token,
                'tokenSecret' => isset($user->tokenSecret) ? $user->tokenSecret : null,
                'provider_id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'nickname' => $user->getNickname(),
                'refresh_token' => $refreshToken,
                'expires_in' => $expiresIn
            ]);

            $serverProvider->save();

            return back()->with('success', 'You have connected your '.$provider.' account');
            
        } else {
            if(!User::where('email', $user->getEmail())->first()) {
                dd('This is a login user provider');
            }
        }
    }
}