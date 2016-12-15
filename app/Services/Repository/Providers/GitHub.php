<?php

namespace App\Services\Repository\Providers;

use App\Models\Site\Site;
use GitHub as GitHubService;
use Github\Exception\RuntimeException;
use App\Exceptions\DeployKeyAlreadyUsed;
use App\Models\User\UserRepositoryProvider;
use Github\Exception\ValidationFailedException;

class GitHub implements RepositoryContract
{
    use RepositoryTrait;

    /**
     * Imports a deploy key so we can clone the repositories.
     * @param Site $site
     * @return string|void
     * @throws \Exception
     */
    public function importSshKeyIfPrivate(Site $site)
    {
        $this->setToken($site->userRepositoryProvider);

        $repository = $site->repository;

        try {
            GitHubService::api('repo')->keys()->create(
                $this->getRepositoryUser($repository),
                $this->getRepositorySlug($repository), [
                    'title' => 'CodePier',
                    'key'   => $site->public_ssh_key,
                ]
            );
        } catch (ValidationFailedException $e) {
            if (! $e->getMessage() == 'Validation Failed: key is already in use') {
                throw new \Exception($e->getMessage());
            }
            throw new DeployKeyAlreadyUsed();
        }
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

        $repositoryInfo = $this->getRepositoryInfo($site->repository);

        if (isset($repositoryInfo['private'])) {
            return $repositoryInfo['private'];
        }

        return false;
    }

    /**
     * Gets the repository information.
     *
     * @param $repository
     *
     * @return mixed
     */
    public function getRepositoryInfo($repository)
    {
        return GitHubService::api('repo')->show(
            $this->getRepositoryUser($repository),
            $this->getRepositorySlug($repository)
        );
    }

    /**
     * Sets the token so we can connect to the users account.
     *
     * @param UserRepositoryProvider $userRepositoryProvider
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function setToken(UserRepositoryProvider $userRepositoryProvider)
    {
        return config(['github.connections.main.token' => $userRepositoryProvider->token]);
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

        $lastCommit = null;

        try {
            $lastCommit = collect(GitHubService::api('repo')->commits()->all($this->getRepositoryUser($repository), $this->getRepositorySlug($repository), ['sha' => $branch]))->first();
        } catch (RuntimeException $e) {
        }

        if (! empty($lastCommit)) {
            dd($lastCommit);
            return [
                'branch '=> $lastCommit['branch'],
                'git_commit' => $lastCommit['sha'],
                'commit_message' => $lastCommit['commit']['message'],
            ];
        }
    }

    public function createDeployHook(Site $site)
    {
        $this->setToken($site->userRepositoryProvider);

        $webhook = GitHubService::api('repo')->hooks()->create($this->getRepositoryUser($site->repository), $this->getRepositorySlug($site->repository), [
            'name'   => 'web',
            'active' => true,
            'events' => [
                'push',
            ],
            'config' => [
                'url'          => action('WebHookController@deploy', $site->encode()),
                'content_type' => 'json',
            ],
        ]);

        $site->automatic_deployment_id = $webhook['id'];
        $site->save();

        return $site;
    }

    public function deleteDeployHook(Site $site)
    {
        $this->setToken($site->userRepositoryProvider);

        GitHubService::api('repo')->hooks()->remove(
            $this->getRepositoryUser($site->repository),
            $this->getRepositorySlug($site->repository),
            $site->automatic_deployment_id
        );

        $site->automatic_deployment_id = null;
        $site->save();

        return $site;
    }
}
