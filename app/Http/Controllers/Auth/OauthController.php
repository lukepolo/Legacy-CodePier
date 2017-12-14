<?php

namespace App\Http\Controllers\Auth;

use Socialite;
use App\Models\User\User;
use Illuminate\Http\Request;
use App\Models\RepositoryProvider;
use App\SocialProviders\TokenData;
use App\Http\Controllers\Controller;
use App\Models\NotificationProvider;
use App\Models\User\UserLoginProvider;
use App\Models\User\UserServerProvider;
use Illuminate\Support\Facades\Session;
use GuzzleHttp\Exception\ClientException;
use App\Models\User\UserRepositoryProvider;
use App\Models\User\UserNotificationProvider;
use App\Models\Server\Provider\ServerProvider;

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
     * @param Request $request
     * @param $provider
     * @return mixed
     */
    public function newProvider(Request $request, $provider)
    {
        Session::put('url.intended', $request->headers->get('referer'));

        $scopes = null;

        $providerDriver = Socialite::driver($provider);

        switch ($provider) {
            case self::GITHUB:
                $providerDriver->scopes(['write:public_key admin:repo_hook']);
                break;
            case self::DIGITAL_OCEAN:
                $providerDriver->scopes(['read write']);
                break;
            case self::SLACK:
                $providerDriver->scopes(['chat:write:bot', 'channels:write']);
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

                    $socialUser = Socialite::driver($provider)->user();

                    if (! \Auth::user()) {
                        $userProvider = UserLoginProvider::withTrashed()
                            ->with('user')
                            ->has('user')
                            ->where('provider', $provider)
                            ->where('provider_id', $socialUser->getId())
                            ->first();

                        if (empty($userProvider)) {
                            $newLoginProvider = $this->createLoginProvider($provider, $socialUser);
                            $newUserModel = $this->createUser($socialUser, $newLoginProvider);
                            \Auth::loginUsingId($newUserModel->id, true);
                        } else {
                            if ($userProvider->deleted_at) {
                                $userProvider->restore();
                            }

                            \Auth::loginUsingId($userProvider->user->id, true);
                        }
                    }

                    if (in_array($provider, static::$repositoryProviders)) {
                        $newUserRepositoryProvider = $this->saveRepositoryProvider($provider, $socialUser);
                    }

                    if (in_array($provider, static::$serverProviders)) {
                        $newUserServerProvider = $this->saveServerProvider($provider, $socialUser);
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
                dd($e->getMessage());
            }

            if (\Auth::check()) {
                return redirect()->intended()->withErrors($e->getMessage());
            } else {
                return redirect('/login')->withErrors($e->getMessage());
            }
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
        return User::create([
            'email'                  => $user->getEmail(),
            'name'                   => empty($user->getName()) ? $user->getEmail() : $user->getName(),
            'user_login_provider_id' => $userLoginProvider->id,
        ]);
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
     * @param $socialUser
     *
     * @return mixed
     */
    private function createLoginProvider($provider, $socialUser)
    {
        $userLoginProvider = UserLoginProvider::withTrashed()->firstOrNew([
            'provider'    => $provider,
            'provider_id' => $socialUser->getId(),
        ]);

        $userLoginProvider->fill([
            'token'         => $socialUser->token,
            'expires_at'    => isset($socialUser->expiresIn) ? $socialUser->expiresIn : null,
            'refresh_token' => isset($socialUser->refreshToken) ? $socialUser->refreshToken : null,
            'token_secret'   => isset($socialUser->tokenSecret) ? $socialUser->tokenSecret : null,
        ]);

        $userLoginProvider->save();

        return $userLoginProvider;
    }

    /**
     * Saves the users repository provider.
     *
     * @param $provider
     * @param $socialUser
     *
     * @return mixed
     */
    private function saveRepositoryProvider($provider, $socialUser)
    {
        $userRepositoryProvider = UserRepositoryProvider::withTrashed()->firstOrNew([
            'repository_provider_id' => RepositoryProvider::where('provider_name', $provider)->first()->id,
            'provider_id'            => $socialUser->getId(),
        ]);

        $userRepositoryProvider->fill([
            'token'         => $socialUser->token,
            'user_id'       => \Auth::user()->id,
            'expires_at'    => isset($socialUser->expiresIn) ? $socialUser->expiresIn : null,
            'refresh_token' => isset($socialUser->refreshToken) ? $socialUser->refreshToken : null,
            'token_secret'   => isset($socialUser->tokenSecret) ? $socialUser->tokenSecret : null,
        ]);

        $userRepositoryProvider->save();

        $userRepositoryProvider->restore();

        return $userRepositoryProvider;
    }

    /**
     * Saves the users server provider.
     *
     * @param $provider
     * @param $socialUser
     *
     * @return mixed
     */
    private function saveServerProvider($provider, $socialUser)
    {
        $userServerProvider = UserServerProvider::withTrashed()->firstOrNew([
            'server_provider_id' => ServerProvider::where('provider_name', $provider)->first()->id,
            'provider_id'        => $socialUser->getId(),
        ]);

        switch ($userServerProvider->serverProvider->provider_name) {
            case self::DIGITAL_OCEAN:
                $token = $socialUser->accessTokenResponseBody['access_token'];
                $refreshToken = $socialUser->accessTokenResponseBody['refresh_token'];
                $expiresIn = $socialUser->accessTokenResponseBody['expires_in'];
                break;
            default:
                // TODO - other server providers
                dd($socialUser);
                break;
        }

        $userServerProvider->fill([
            'token'         => $token,
            'expires_at'    => $expiresIn,
            'user_id'       => \Auth::user()->id,
            'refresh_token' => $refreshToken,
            'token_secret'   => isset($socialUser->tokenSecret) ? $socialUser->tokenSecret : null,
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
            'expires_at'    => isset($tokenData->expiresIn) ? $tokenData->expiresIn : null,
            'refresh_token' => isset($tokenData->refreshToken) ? $tokenData->refreshToken : null,
            'token_secret'   => isset($tokenData->tokenSecret) ? $tokenData->tokenSecret : null,
        ]);

        $userNotificationProvider->save();

        $userNotificationProvider->restore();

        return $userNotificationProvider;
    }
}
