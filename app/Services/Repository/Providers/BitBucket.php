<?php

namespace App\Services\Repository\Providers;

use Bitbucket\API\User;
use App\Models\Site\Site;
use Bitbucket\API\Repositories\Hooks;
use Bitbucket\API\Repositories\Commits;
use Bitbucket\API\Repositories\Deploykeys;
use App\Models\User\UserRepositoryProvider;
use Bitbucket\API\Http\Listener\OAuthListener;

class BitBucket implements RepositoryContract
{
    use RepositoryTrait;

    private $oauthParams;

    /**
     * Imports a deploy key so we can clone the repositories.
     * @param Site $site
     */
    public function importSshKeyIfPrivate(Site $site)
    {
        $repository = $site->repository;

        $this->setToken($site->userRepositoryProvider);

        $user = new User();

        $user->getClient()->addListener(
            new OAuthListener($this->oauthParams)
        );

        $slug = $this->getRepositorySlug($repository);

        $repositories = collect(json_decode($user->repositories()->get()->getContent(), true));

        $repositoryInfo = $repositories->first(function ($repository) use ($slug) {
            return $repository['slug'] == $slug;
        });

        if ($repositoryInfo['is_private']) {
            $this->isPrivate($site, true);

            $deployKey = new Deploykeys();

            $deployKey->getClient()->addListener(
                new OAuthListener($this->oauthParams)
            );

            $deployKey->create($this->getRepositoryUser($repository), $this->getRepositorySlug($repository), $site->public_ssh_key, 'https://codepier.io');

            return;
        }

        $this->isPrivate($site, false);
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
            'oauth_consumer_key'    => env('OAUTH_BITBUCKET_CLIENT_ID'),
            'oauth_consumer_secret' => env('OAUTH_BITBUCKET_SECRET_ID'),
            'oauth_callback'        => env('OAUTH_BITBUCKET_CALLBACK'),
        ];
    }

    /**
     * Gets the users repositories username // TODO - move to a trait.
     *
     * @param $repository
     *
     * @return mixed
     */
    public function getRepositoryUser($repository)
    {
        return explode('/', $repository)[0];
    }

    /**
     * Gets the users repositories name // TODO - move to a trait.
     *
     * @param $repository
     *
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
            'branch' => $branch,
        ]);

        $lastCommit = collect(json_decode($commits->getContent())->values)->first();

        if (! empty($lastCommit)) {
            if (! empty($lastCommit)) {
                return [
                    'git_commit' => $lastCommit->hash,
                    'commit_message' => $lastCommit->message,
                ];
            }
        }
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
            'url'         => action('WebHookController@deploy', $site->encode()),
            'active'      => true,
            'events'      => [
                'repo:push',
                'pullrequest:fulfilled',
            ],
        ]);

        $site->automatic_deployment_id = json_decode($response->getContent())->uuid;
        $site->save();

        return $site;
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

        return $site;
    }
}
