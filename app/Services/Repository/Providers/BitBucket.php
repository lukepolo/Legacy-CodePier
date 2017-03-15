<?php

namespace App\Services\Repository\Providers;

use App\Exceptions\DeployHookFailed;
use Bitbucket\API\User;
use App\Models\Site\Site;
use Bitbucket\API\Users\SshKeys;
use Bitbucket\API\Repositories\Hooks;
use Bitbucket\API\Repositories\Repository;
use App\Models\User\UserRepositoryProvider;
use Bitbucket\API\Http\Listener\OAuthListener;
use Buzz\Message\Response;

class BitBucket implements RepositoryContract
{
    use RepositoryTrait;

    private $oauthParams;

    /**
     * Imports a deploy key so we can clone the repositories.
     * @param Site $site
     */
    public function importSshKey(Site $site)
    {
        $this->setToken($site->userRepositoryProvider);

        $user = new User();

        $user->getClient()->addListener(
            new OAuthListener($this->oauthParams)
        );

        $sshKeys = new SshKeys();

        $sshKeys->getClient()->addListener(
            new OAuthListener($this->oauthParams)
        );

        $sshKeys->create(json_decode($user->get()->getContent())->user->username, $site->public_ssh_key, $this->sshKeyLabel($site));
    }

    /**
     * Checks if the repository is private.
     *
     * @param Site $site
     *
     * @return bool
     */
    public function isPrivate(Site $site)
    {
        $this->setToken($site->userRepositoryProvider);

        $user = new User();

        $user->getClient()->addListener(
            new OAuthListener($this->oauthParams)
        );

        $repositories = new Repository();

        $repository = json_decode($repositories->get(
            $this->getRepositoryUser($site->repository),
            $this->getRepositorySlug($site->repository)
        )->getContent());

        if (empty($repository)) {
            return true;
        }

        return false;
    }

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
            'oauth_token'           => $userRepositoryProvider->token,
            'oauth_token_secret'    => $userRepositoryProvider->tokenSecret,
            'oauth_consumer_key'    => config('services.bitbucket.client_id'),
            'oauth_consumer_secret' => config('services.bitbucket.client_secret.client_id'),
            'oauth_callback'        => config('services.bitbucket.redirect'),
        ];
    }

    /**
     * @param Site $site
     * @return Site
     * @throws DeployHookFailed
     */
    public function createDeployHook(Site $site)
    {
        $this->setToken($site->userRepositoryProvider);

        $hook = new Hooks();

        $hook->getClient()->addListener(
            new OAuthListener($this->oauthParams)
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

        if($response->getStatusCode() == 401) {
            if($site->private) {
                throw new DeployHookFailed('We could not create the webhook, please make sure you have access to the repository');
            }
            throw new DeployHookFailed('We could not create the webhook as it is not owned by you. Please fork the repository ('.$owner.'/'.$slug.') to allow for this feature.');
        }

        $site->automatic_deployment_id = json_decode($response->getContent())->uuid;
        $site->save();

        return $site;
    }

    /**
     * @param Site $site
     * @return Site
     */
    public function deleteDeployHook(Site $site)
    {
        $this->setToken($site->userRepositoryProvider);

        $hook = new Hooks();

        $hook->getClient()->addListener(
            new OAuthListener($this->oauthParams)
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
