<?php

namespace App\Services\Server\Site\Repository\Providers;

use App\Models\Site;
use App\Models\UserRepositoryProvider;
use Bitbucket\API\Http\Listener\OAuthListener;
use Bitbucket\API\Repositories\Commits;
use Bitbucket\API\Repositories\Deploykeys;
use Bitbucket\API\Repositories\Hooks;
use Bitbucket\API\User;
use Bitbucket\API\Users;
use GitHub as GitHubService;

/**
 * Class BitBucket
 * @package App\Services\Server\Site\Repository\Providers
 */
class BitBucket implements RepositoryContract
{
    private $oauthParams;

    /**
     * Imports a deploy key so we can clone the repositories
     *
     * @param UserRepositoryProvider $userRepositoryProvider
     * @param $repository
     * @param $sshKey
     * @throws \Exception
     */
    public function importSshKeyIfPrivate(UserRepositoryProvider $userRepositoryProvider, $repository, $sshKey)
    {
        $this->setToken($userRepositoryProvider);

        $user = new User();

        $user->getClient()->addListener(
            new OAuthListener($this->oauthParams)
        );

        $slug = $this->getRepositorySlug($repository);

        $repositories = collect(json_decode($user->repositories()->get()->getContent(), true));

        $repositoryInfo = $repositories->first(function($key, $repository) use($slug) {
            return $repository['slug'] == $slug;
        });

        if ($repositoryInfo['is_private']) {

            $deployKey = new Deploykeys();

            $deployKey->getClient()->addListener(
                new OAuthListener($this->oauthParams)
            );

            $deployKey->create($this->getRepositoryUser($repository), $this->getRepositorySlug($repository), $sshKey, 'https://codepier.io');
        }
    }

    /**
     * Sets the token so we can connect to the users account
     * @param UserRepositoryProvider $userRepositoryProvider
     * @return mixed
     * @throws \Exception
     */
    public function setToken(UserRepositoryProvider $userRepositoryProvider)
    {
        $this->oauthParams = [
            'oauth_token' => $userRepositoryProvider->token,
            'oauth_token_secret' => $userRepositoryProvider->tokenSecret,
            'oauth_consumer_key' => env('OAUTH_BITBUCKET_CLIENT_ID'),
            'oauth_consumer_secret' => env('OAUTH_BITBUCKET_SECRET_ID'),
            'oauth_callback' => env('OAUTH_BITBUCKET_CALLBACK'),
        ];
    }

    /**
     * Gets the users repositories username // TODO - move to a trait
     * @param $repository
     * @return mixed
     */
    public function getRepositoryUser($repository)
    {
        return explode('/', $repository)[0];
    }

    /**
     * Gets the users repositories name // TODO - move to a trait
     * @param $repository
     * @return mixed
     */
    public function getRepositorySlug($repository)
    {
        return explode('/', $repository)[1];
    }

    public function getLatestCommit(UserRepositoryProvider $userRepositoryProvider, $repository, $branch)
    {
        $this->setToken($userRepositoryProvider);

        $commits = new Commits();

        $commits->getClient()->addListener(
            new OAuthListener($this->oauthParams)
        );

        $commits = $commits->all($this->getRepositoryUser($repository), $this->getRepositorySlug($repository), [
            'branch' => $branch
        ]);

        $lastCommit = collect(json_decode($commits->getContent())->values)->first();

        if(!empty($lastCommit)) {
            return $lastCommit->hash;
        }

        return null;
    }

    public function createDeployHook(Site $site)
    {
        $this->setToken($site->userRepositoryProvider);

        $hook = new Hooks();

        $hook->getClient()->addListener(
            new OAuthListener($this->oauthParams)
        );

        $response = $hook->create(
            $this->getRepositoryUser($site->repository),
            $this->getRepositorySlug($site->repository), [
            'description' => 'CodePier',
            'url' => route('webhook/deploy', $site->encode()),
            'active' => true,
            'events' => [
                'repo:push',
                'pullrequest:fulfilled'
            ]
        ]);

        $site->automatic_deployment_id = json_decode($response->getContent())->uuid;
        $site->save();
    }

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

    }
}