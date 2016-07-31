<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\NotificationProvider;
use App\Models\RepositoryProvider;
use App\Models\ServerProvider;
use App\Models\User;
use App\Models\UserLoginProvider;
use App\Models\UserNotificationProvider;
use App\Models\UserRepositoryProvider;
use App\Models\UserServerProvider;
use App\SocialProviders\TokenData;
use Bitbucket\API\Http\Listener\OAuthListener;
use Bitbucket\API\Users;
use Socialite;

/**
 * Class OauthController
 * @package app\Http\Controllers\Auth
 */
class OauthController extends Controller
{

    CONST SLACK = 'slack';
    CONST GITHUB = 'github';
    CONST BITBUCKET = 'bitbucket';
    CONST DIGITAL_OCEAN = 'digitalocean';

    public static $serverProviders = [
        self::DIGITAL_OCEAN
    ];

    public static $repositoryProviders = [
        self::GITHUB,
        self::BITBUCKET
    ];

    public static $notificationProviders = [
        self::SLACK
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
            case self::GITHUB :
                $providerDriver->scopes(['write:public_key admin:repo_hook repo']);
                break;
            case self::DIGITAL_OCEAN :
                $providerDriver->scopes(['read write']);
                break;
            case self::SLACK :
                $providerDriver->scopes(['channels:read chat:write:bot']);
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
        try {
            switch ($provider) {
                case self::SLACK :
                    $tokenData = Socialite::driver($provider)->getAccessTokenResponse(\Request::get('code'));

                    $newUserNotificationProvider = $this->saveNotificationProvider($provider,
                        new TokenData($tokenData['access_token'], $tokenData['user_id']));
                    break;
                default :
                    $user = Socialite::driver($provider)->user();

                    if (!\Auth::user()) {
                        if (!$userProvider = UserLoginProvider::has('user')->where('provider_id',
                            $user->getId())->first()
                        ) {
                            $newLoginProvider = $this->createLoginProvider($provider, $user);
                            $newUserModel = $this->createUser($user, $newLoginProvider);
                            \Auth::loginUsingId($newUserModel->id);
                        } else {
                            \Auth::loginUsingId($userProvider->user->id);
                        }
                    }

                    if (in_array($provider, static::$repositoryProviders)) {
                        $newUserRepositoryProvider = $this->saveRepositoryProvider($provider, $user);
                    }

                    if (in_array($provider, static::$serverProviders)) {
                        $newUserServerProvider = $this->saveServerProvider($provider, $user);
                    }

                    break;
            }

            return redirect()->intended('/');

        } catch (\Exception $e) {

            if (!empty($newLoginProvider)) {
                $newLoginProvider->delete();
            }

            if (!empty($newUserModel)) {
                $newUserModel->delete();
            }

            if (!empty($newUserRepositoryProvider)) {
                $newUserRepositoryProvider->delete();
            }

            if (!empty($newUserServerProvider)) {
                $newUserServerProvider->delete();
            }


            if (!empty($newUserNotificationProvider)) {
                $newUserNotificationProvider->delete();
            }

            return redirect(\Auth::check() ? action('Auth\UserController@getMyProfile') : '/login')->withErrors($e->getMessage());
        }
    }

    /**
     * Creates a new user
     * @param $user
     * @param UserLoginProvider $userLoginProvider
     * @return mixed
     * @throws \Exception
     */
    public function createUser($user, UserLoginProvider $userLoginProvider)
    {
        switch ($userLoginProvider->provider) {
            case self::BITBUCKET :

                $oauth_params = [
                    'oauth_token' => $user->token,
                    'oauth_token_secret' => $user->tokenSecret,
                    'oauth_consumer_key' => env('OAUTH_BITBUCKET_CLIENT_ID'),
                    'oauth_consumer_secret' => env('OAUTH_BITBUCKET_SECRET_ID'),
                    'oauth_callback' => env('OAUTH_BITBUCKET_CALLBACK'),
                ];

                $users = new Users();

                $users->getClient()->addListener(
                    new OAuthListener($oauth_params)
                );

                // now you can access protected endpoints as consumer owner
                $response = $users->emails()->all($user->id);

                if ($response->getStatusCode() != '200') {
                    throw new \Exception('Unable to get email from Bitbucket API');
                }

                $email = collect(json_decode($response->getContent(), true))->first(function ($key, $email) {
                    return $email['primary'];
                })['email'];

                break;
            default:
                $email = $user->getEmail();
                break;
        }

        $userModel = User::create([
            'email' => $email,
            'name' => empty($user->getName()) ? $user->getEmail() : $user->getName(),
            'user_login_provider_id' => $userLoginProvider->id
        ]);

        return $userModel;
    }

    public function getDisconnectService($providerType, int $serviceID)
    {
        if (UserRepositoryProvider::class == $providerType) {
            if (!empty($userRepositoryProvider = \Auth::user()->userRepositoryProviders->where('id',
                $serviceID)->first())
            ) {
                $userRepositoryProvider->delete();
            }
        }

        if (UserServerProvider::class == $providerType) {
            if (!empty($userServerProvider = \Auth::user()->userServerProviders->where('id', $serviceID)->first())) {
                $userServerProvider->delete();
            }
        }

        if (UserNotificationProvider::class == $providerType) {
            if (!empty($userNotificationProvider = \Auth::user()->userNotificationProviders->where('id',
                $serviceID)->first())
            ) {
                $userNotificationProvider->delete();
            }
        }

        return back()->with('success', 'You have disconnected the service');
    }

    /**
     * Creates a login provider
     * @param $provider
     * @param $user
     * @return mixed
     */
    private function createLoginProvider($provider, $user)
    {
        $userLoginProvider = UserLoginProvider::withTrashed()->firstOrNew([
            'provider' => $provider,
            'provider_id' => $user->getId(),
        ]);

        $userLoginProvider->fill([
            'token' => $user->token,
            'expires_in' => isset($user->expiresIn) ? $user->expiresIn : null,
            'refresh_token' => isset($user->refreshToken) ? $user->refreshToken : null,
            'tokenSecret' => isset($user->tokenSecret) ? $user->tokenSecret : null
        ]);

        $userLoginProvider->save();

        $userLoginProvider->restore();

        return $userLoginProvider;
    }

    /**
     * Saves the users repository provider
     * @param $provider
     * @param $user
     * @return mixed
     */
    private function saveRepositoryProvider($provider, $user)
    {
        $userRepositoryProvider = UserRepositoryProvider::withTrashed()->firstOrNew([
            'repository_provider_id' => RepositoryProvider::where('provider_name', $provider)->first()->id,
            'provider_id' => $user->getId()
        ]);

        $userRepositoryProvider->fill([
            'token' => $user->token,
            'user_id' => \Auth::user()->id,
            'expires_in' => isset($user->expiresIn) ? $user->expiresIn : null,
            'refresh_token' => isset($user->refreshToken) ? $user->refreshToken : null,
            'tokenSecret' => isset($user->tokenSecret) ? $user->tokenSecret : null
        ]);

        $userRepositoryProvider->save();

        $userRepositoryProvider->restore();

        return $userRepositoryProvider;
    }

    /**
     * Saves the users server provider
     * @param $provider
     * @param $user
     * @return mixed
     */
    private function saveServerProvider($provider, $user)
    {
        $userServerProvider = UserServerProvider::withTrashed()->firstOrNew([
            'server_provider_id' => ServerProvider::where('provider_name', $provider)->first()->id,
            'provider_id' => $user->getId(),
        ]);

        switch ($userServerProvider->serverProvider->provider_name) {
            case self::DIGITAL_OCEAN :
                $token = $user->accessTokenResponseBody['access_token'];
                $refreshToken = $user->accessTokenResponseBody['refresh_token'];
                $expiresIn = $user->accessTokenResponseBody['expires_in'];
                break;
        }

        $userServerProvider->fill([
            'token' => $token,
            'expires_in' => $expiresIn,
            'user_id' => \Auth::user()->id,
            'refresh_token' => $refreshToken,
            'tokenSecret' => isset($user->tokenSecret) ? $user->tokenSecret : null
        ]);

        $userServerProvider->save();

        $userServerProvider->restore();

        return $userServerProvider;
    }

    private function saveNotificationProvider($provider, TokenData $tokenData)
    {
        $userNotificationProvider = UserNotificationProvider::withTrashed()->firstOrNew([
            'notification_provider_id' => NotificationProvider::where('provider_name', $provider)->first()->id,
            'provider_id' => $tokenData->userID
        ]);

        $userNotificationProvider->fill([
            'token' => $tokenData->token,
            'user_id' => \Auth::user()->id,
            'expires_in' => isset($tokenData->expiresIn) ? $tokenData->expiresIn : null,
            'refresh_token' => isset($tokenData->refreshToken) ? $tokenData->refreshToken : null,
            'tokenSecret' => isset($tokenData->tokenSecret) ? $tokenData->tokenSecret : null
        ]);

        $userNotificationProvider->save();

        $userNotificationProvider->restore();

        return $userNotificationProvider;
    }
}