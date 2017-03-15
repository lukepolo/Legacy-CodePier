<?php

namespace App\Http\Controllers\Auth;

use Socialite;
use Bitbucket\API\Users;
use App\Models\User\User;
use Illuminate\Http\Request;
use App\Models\RepositoryProvider;
use App\SocialProviders\TokenData;
use App\Http\Controllers\Controller;
use App\Models\NotificationProvider;
use App\Models\User\UserLoginProvider;
use App\Models\User\UserServerProvider;
use GuzzleHttp\Exception\ClientException;
use App\Models\User\UserRepositoryProvider;
use App\Models\User\UserNotificationProvider;
use App\Models\Server\Provider\ServerProvider;
use Bitbucket\API\Http\Listener\OAuthListener;

class OauthController extends Controller
{
    const SLACK = 'slack';
    const GITHUB = 'github';
    const GITLAB = 'gitlab';
    const BITBUCKET = 'bitbucket';
    const DIGITAL_OCEAN = 'digitalocean';

    public static $serverProviders = [
        self::DIGITAL_OCEAN,
    ];

    public static $repositoryProviders = [
        self::GITHUB,
        self::GITLAB,
        self::BITBUCKET,
    ];

    public static $notificationProviders = [
        self::SLACK,
    ];

    /**
     * Handles provider requests.
     *
     * @param $provider
     *
     * @return mixed
     */
    public function newProvider($provider)
    {
        $scopes = null;

        $providerDriver = Socialite::driver($provider);

        switch ($provider) {
            case self::GITHUB:
                $providerDriver->scopes(['write:public_key write:repo_hook']);
                break;
            case self::DIGITAL_OCEAN:
                $providerDriver->scopes(['read write']);
                break;
            case self::SLACK:
                $providerDriver->scopes(['chat:write:bot']);
                break;
        }

        return $providerDriver->redirect();
    }

    /**
     * Handles the request from the provider.
     *
     * @param Request $request
     * @param $provider
     * @return mixed
     */
    public function getHandleProviderCallback(Request $request, $provider)
    {
        try {
            switch ($provider) {
                case self::SLACK:
                    $tokenData = Socialite::driver($provider)->getAccessTokenResponse($request->get('code'));
                    \Log::debug($tokenData);
                    $newUserNotificationProvider = $this->saveNotificationProvider($provider,
                        new TokenData($tokenData['access_token'], $tokenData['user_id']));
                    break;
                default:

                    $user = Socialite::driver($provider)->user();

                    if (! \Auth::user()) {
                        if (! $userProvider = UserLoginProvider::has('user')->where('provider_id', $user->getId())->first()) {
                            $newLoginProvider = $this->createLoginProvider($provider, $user);
                            $newUserModel = $this->createUser($user, $newLoginProvider);
                            \Auth::loginUsingId($newUserModel->id, true);
                        } else {
                            \Auth::loginUsingId($userProvider->user->id, true);
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
            if (! empty($newLoginProvider)) {
                $newLoginProvider->delete();
            }

            if (! empty($newUserModel)) {
                $newUserModel->delete();
            }

            if (! empty($newUserRepositoryProvider)) {
                $newUserRepositoryProvider->delete();
            }

            if (! empty($newUserServerProvider)) {
                $newUserServerProvider->delete();
            }

            if (! empty($newUserNotificationProvider)) {
                $newUserNotificationProvider->delete();
            }

            if (config('app.env') === 'local') {
                /* @var ClientException $e */
                dump($e->getRequest());
                dd($e->getResponse()->getBody()->getContents());
            }

            return redirect(\Auth::check() ? url('my/account') : '/login')->withErrors($e->getMessage());
        }
    }

    /**
     * Creates a new user.
     *
     * @param $user
     * @param UserLoginProvider $userLoginProvider
     *
     * @return mixed
     * @throws \Exception
     */
    public function createUser($user, UserLoginProvider $userLoginProvider)
    {
        switch ($userLoginProvider->provider) {
            case self::BITBUCKET:

                $oauth_params = [
                    'oauth_token'           => $user->token,
                    'oauth_token_secret'    => $user->tokenSecret,
                    'oauth_consumer_key'    => config('services.bitbucket.client_id'),
                    'oauth_consumer_secret' => config('services.bitbucket.client_secret'),
                    'oauth_callback'        => config('services.bitbucket.redirect'),
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

                $email = collect(json_decode($response->getContent(), true))->first(function ($email) {
                    return $email['primary'];
                })['email'];

                break;
            default:
                $email = $user->getEmail();
                break;
        }

        $userModel = User::create([
            'email'                  => $email,
            'name'                   => empty($user->getName()) ? $user->getEmail() : $user->getName(),
            'user_login_provider_id' => $userLoginProvider->id,
        ]);

        return $userModel;
    }

    /**
     * Disconnects a service provider.
     *
     * @param $providerType
     * @param int $serviceID
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDisconnectService($providerType, int $serviceID)
    {
        if (UserRepositoryProvider::class == $providerType) {
            if (! empty($userRepositoryProvider = \Auth::user()->userRepositoryProviders->where('id',
                $serviceID)->first())
            ) {
                $userRepositoryProvider->delete();
            }
        }

        if (UserServerProvider::class == $providerType) {
            if (! empty($userServerProvider = \Auth::user()->userServerProviders->where('id', $serviceID)->first())) {
                $userServerProvider->delete();
            }
        }

        if (UserNotificationProvider::class == $providerType) {
            if (! empty($userNotificationProvider = \Auth::user()->userNotificationProviders->where('id',
                $serviceID)->first())
            ) {
                $userNotificationProvider->delete();
            }
        }

        return response()->json('OK');
    }

    /**
     * Creates a login provider.
     *
     * @param $provider
     * @param $user
     *
     * @return mixed
     */
    private function createLoginProvider($provider, $user)
    {
        $userLoginProvider = UserLoginProvider::withTrashed()->firstOrNew([
            'provider'    => $provider,
            'provider_id' => $user->getId(),
        ]);

        $userLoginProvider->fill([
            'token'         => $user->token,
            'expires_in'    => isset($user->expiresIn) ? $user->expiresIn : null,
            'refresh_token' => isset($user->refreshToken) ? $user->refreshToken : null,
            'tokenSecret'   => isset($user->tokenSecret) ? $user->tokenSecret : null,
        ]);

        $userLoginProvider->save();

        $userLoginProvider->restore();

        return $userLoginProvider;
    }

    /**
     * Saves the users repository provider.
     *
     * @param $provider
     * @param $user
     *
     * @return mixed
     */
    private function saveRepositoryProvider($provider, $user)
    {
        $userRepositoryProvider = UserRepositoryProvider::withTrashed()->firstOrNew([
            'repository_provider_id' => RepositoryProvider::where('provider_name', $provider)->first()->id,
            'provider_id'            => $user->getId(),
        ]);

        $userRepositoryProvider->fill([
            'token'         => $user->token,
            'user_id'       => \Auth::user()->id,
            'expires_in'    => isset($user->expiresIn) ? $user->expiresIn : null,
            'refresh_token' => isset($user->refreshToken) ? $user->refreshToken : null,
            'tokenSecret'   => isset($user->tokenSecret) ? $user->tokenSecret : null,
        ]);

        $userRepositoryProvider->save();

        $userRepositoryProvider->restore();

        return $userRepositoryProvider;
    }

    /**
     * Saves the users server provider.
     *
     * @param $provider
     * @param $user
     *
     * @return mixed
     */
    private function saveServerProvider($provider, $user)
    {
        $userServerProvider = UserServerProvider::withTrashed()->firstOrNew([
            'server_provider_id' => ServerProvider::where('provider_name', $provider)->first()->id,
            'provider_id'        => $user->getId(),
        ]);

        switch ($userServerProvider->serverProvider->provider_name) {
            case self::DIGITAL_OCEAN:
                $token = $user->accessTokenResponseBody['access_token'];
                $refreshToken = $user->accessTokenResponseBody['refresh_token'];
                $expiresIn = $user->accessTokenResponseBody['expires_in'];
                break;
        }

        $userServerProvider->fill([
            'token'         => $token,
            'expires_in'    => $expiresIn,
            'user_id'       => \Auth::user()->id,
            'refresh_token' => $refreshToken,
            'tokenSecret'   => isset($user->tokenSecret) ? $user->tokenSecret : null,
        ]);

        $userServerProvider->save();

        $userServerProvider->restore();

        return $userServerProvider;
    }

    /**
     * Saves the users notification provider.
     *
     * @param $provider
     * @param TokenData $tokenData
     *
     * @return mixed
     */
    private function saveNotificationProvider($provider, TokenData $tokenData)
    {
        $userNotificationProvider = UserNotificationProvider::withTrashed()->firstOrNew([
            'notification_provider_id' => NotificationProvider::where('provider_name', $provider)->first()->id,
            'provider_id'              => $tokenData->userID,
        ]);

        $userNotificationProvider->fill([
            'token'         => $tokenData->token,
            'user_id'       => \Auth::user()->id,
            'expires_in'    => isset($tokenData->expiresIn) ? $tokenData->expiresIn : null,
            'refresh_token' => isset($tokenData->refreshToken) ? $tokenData->refreshToken : null,
            'tokenSecret'   => isset($tokenData->tokenSecret) ? $tokenData->tokenSecret : null,
        ]);

        $userNotificationProvider->save();

        $userNotificationProvider->restore();

        return $userNotificationProvider;
    }
}
