<?php

namespace App\Services\Repository\Providers;

use App\Exceptions\DeployHookFailed;
use App\Models\Site\Site;
use App\Models\User\UserRepositoryProvider;
use Bitbucket\API\Http\Listener\OAuth2Listener;
use Bitbucket\API\Repositories\Hooks;
use Buzz\Message\Response;
use Carbon\Carbon;
use League\OAuth2\Client\Token\AccessToken;

class BitBucket implements RepositoryContract
{
    use RepositoryTrait;

    private $oauthParams;

    /**
     * Sets the token so we can connect to the users account.
     *
     * @param \App\Models\User\UserRepositoryProvider $userRepositoryProvider
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function setToken(UserRepositoryProvider $userRepositoryProvider)
    {
        $this->oauthParams = [
            'access_token'=> $this->getToken($userRepositoryProvider),
        ];
    }

    /**
     * @param Site $site
     *
     * @throws DeployHookFailed
     * @throws \Exception
     *
     * @return Site
     */
    public function createDeployHook(Site $site)
    {
        $this->setToken($site->userRepositoryProvider);

        $hook = new Hooks();

        $hook->getClient()->addListener(
            new OAuth2Listener($this->oauthParams)
        );

        $owner = $this->getRepositoryUser($site->repository);
        $slug = $this->getRepositorySlug($site->repository);

        /** @var Response $response */
        $response = $hook->create(
            $owner,
            $slug, [
            'description' => 'CodePier',
            'url'         => action('WebHookController@deploy', $site->hash),
            'active'      => true,
            'events'      => [
                'repo:push',
            ],
        ]);

        if (401 == $response->getStatusCode()) {
            throw new DeployHookFailed('We could not create the webhook, please make sure you have access to the repository');
        }

        $site->automatic_deployment_id = json_decode($response->getContent())->uuid;
        $site->save();

        return $site;
    }

    /**
     * @param UserRepositoryProvider $userRepositoryProvider
     *
     * @return mixed|string
     */
    public function getToken(UserRepositoryProvider $userRepositoryProvider)
    {
        if ($userRepositoryProvider->isExpired()) {
            $provider = new \Stevenmaguire\OAuth2\Client\Provider\Bitbucket([
                'clientId'          => config('services.bitbucket.client_id'),
                'clientSecret'      => config('services.bitbucket.client_secret'),
                'redirectUri'       => config('services.bitbucket.redirect'),
            ]);

            /** @var AccessToken $token */
            $response = $provider->getAccessToken('refresh_token', [
                'refresh_token' => $userRepositoryProvider->refresh_token,
            ]);

            $userRepositoryProvider->fill([
                'token' => $response->getToken(),
                'refresh_token' => $response->getRefreshToken(),
                'expires_at' => Carbon::createFromTimestampUTC($response->getExpires()),
            ]);

            $userRepositoryProvider->save();
        }

        return $userRepositoryProvider->token;
    }

    /**
     * @param Site $site
     *
     * @throws \Exception
     *
     * @return Site
     */
    public function deleteDeployHook(Site $site)
    {
        $this->setToken($site->userRepositoryProvider);

        $hook = new Hooks();

        $hook->getClient()->addListener(
            new OAuth2Listener($this->oauthParams)
        );

        $hook->delete(
            $this->getRepositoryUser($site->repository),
            $this->getRepositorySlug($site->repository),
            trim($site->automatic_deployment_id, '{,}')
        );

        $site->automatic_deployment_id = null;
        $site->save();

        return $site;
    }
}
