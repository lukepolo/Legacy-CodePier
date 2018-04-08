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
use Illuminate\Support\Facades\Session;
use App\Models\User\UserRepositoryProvider;
use App\Models\User\UserNotificationProvider;

class OauthController extends Controller
{
    const SLACK = 'slack';
    const GITHUB = 'github';
    const GITLAB = 'gitlab';
    const BITBUCKET = 'bitbucket';

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
                $providerDriver->scopes(['repo admin:repo_hook']);
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
                            $alreadyRegistered = User::where('email', $socialUser->getEmail())->first();

                            if (! empty($alreadyRegistered)) {
                                return redirect('/login')->withErrors('You have already registered with this email.');
                            }

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

                    break;
            }

            return redirect()->intended(config('app.url'));
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
                throw $e;
            }

            if (\Auth::check()) {
                return redirect()->back()->withErrors($e->getMessage());
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
            'confirmed'              => true,
            'email'                  => $user->getEmail(),
            'user_login_provider_id' => $userLoginProvider->id,
            'name'                   => empty($user->getName()) ? $user->getEmail() : $user->getName(),
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
