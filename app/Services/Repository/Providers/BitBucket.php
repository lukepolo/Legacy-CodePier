<?php

namespace App\Services\Repository\Providers;

use Bitbucket\API\User;
use App\Models\Site\Site;
use Bitbucket\API\Users\SshKeys;
use Bitbucket\API\Repositories\Hooks;
use Bitbucket\API\Repositories\Commits;
use Bitbucket\API\Repositories\Repository;
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
            'oauth_consumer_key'    => env('OAUTH_BITBUCKET_CLIENT_ID'),
            'oauth_consumer_secret' => env('OAUTH_BITBUCKET_SECRET_ID'),
            'oauth_callback'        => env('OAUTH_BITBUCKET_CALLBACK'),
        ];
    }

    /**
     * @param UserRepositoryProvider $userRepositoryProvider
     * @param $repository
     * @param $branch
     * @return array
     */
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

    /**
     * @param Site $site
     * @return Site
     */
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
            ],
        ]);

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
