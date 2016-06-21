<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ServerProvider;
use App\Models\User;
use App\Models\UserLoginProvider;
use App\Models\UserRepositoryProvider;
use App\Models\UserServerProvider;
use Socialite;

/**
 * Class OauthController
 * @package app\Http\Controllers\Auth
 */
class OauthController extends Controller
{
    public static $serverProviders = [
        'digitalocean'
    ];

    public static $repositoryProviders = [
        'github'
    ];

    /**
     * Handles provider requests
     * @param $provider
     * @return mixed
     */
    public function newProvider($provider)
    {
        $scopes = null;

        $providerDriver = Socialite::driver($provider);

        switch ($provider) {
            case 'github' :
                $providerDriver->scopes(['write:public_key admin:repo_hook repo']);
                break;
            case 'digitalocean':
                $providerDriver->scopes(['read write']);
                break;
        }

        return $providerDriver->redirect();
    }

    /**
     * Handles the request from the provider
     * @param $provider
     * @return mixed
     */
    public function getHandleProviderCallback($provider)
    {
        $user = Socialite::driver($provider)->user();

        try {
            if (!\Auth::user()) {

                if(!$userLoginProvider = UserLoginProvider::where('provider_id', $user->getId())->first()) {
                    $userLoginProvider = UserLoginProvider::create([
                        'provider' => $provider,
                        'provider_id' => $user->getId()
                    ]);

                    $userModel = User::create([
                        'name' => empty($user->getName()) ? $user->getEmail() : $user->getName(),
                        'email' => $user->getEmail(),
                        'user_login_provider_id' => $userLoginProvider->id
                    ]);
                }

                \Auth::loginUsingId($userLoginProvider->user->id);
            }

            if (in_array($provider, static::$repositoryProviders)) {
                $userRepositoryProvider = UserRepositoryProvider::firstOrNew([
                    'service' => $provider,
                    'user_id' => \Auth::user()->id,
                ]);

                $token = $user->token;
                $refreshToken = $user->refreshToken;
                $expiresIn = $user->expiresIn;

                $userRepositoryProvider->fill([
                    'token' => $token,
                    'tokenSecret' => isset($user->tokenSecret) ? $user->tokenSecret : null,
                    'provider_id' => $user->getId(),
                    'name' => $user->getName(),
                    'email' => $user->getEmail(),
                    'nickname' => $user->getNickname(),
                    'refresh_token' => $refreshToken,
                    'expires_in' => $expiresIn
                ]);

                $userRepositoryProvider->save();
            }

            if (in_array($provider, static::$serverProviders)) {
                $userServerProvider = UserServerProvider::firstOrNew([
                    'server_provider_id' => ServerProvider::where('provider_name', $provider)->first()->id,
                    'user_id' => \Auth::user()->id,
                ]);

                $token = $user->accessTokenResponseBody['access_token'];
                $refreshToken = $user->accessTokenResponseBody['refresh_token'];
                $expiresIn = $user->accessTokenResponseBody['expires_in'];

                $userServerProvider->fill([
                    'token' => $token,
                    'tokenSecret' => isset($user->tokenSecret) ? $user->tokenSecret : null,
                    'provider_id' => $user->getId(),
                    'name' => $user->getName(),
                    'email' => $user->getEmail(),
                    'nickname' => $user->getNickname(),
                    'refresh_token' => $refreshToken,
                    'expires_in' => $expiresIn
                ]);

                $userServerProvider->save();
            }

            return back()->with('success', 'You have connected your ' . $provider . ' account');
        } catch(\Exception $e) {
            if(!empty($userLoginProvider)) {
                $userLoginProvider->delete();
            }

            if(!empty($userModel)) {
                $userModel->delete();
            }

            if(!empty($userRepositoryProvider)) {
                $userRepositoryProvider->delete();
            }

            if(!empty($userServerProvider)) {
                $userServerProvider->delete();
            }

            return back()->withErrors($e->getMessage());
        }

    }
}