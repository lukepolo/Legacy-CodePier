<?php

namespace App\Services\Server\Site\Repository\Providers;

use App\Models\UserRepositoryProvider;
use GitHub as GitHubService;
use Github\Exception\ValidationFailedException;

/**
 * Class GitHub
 * @package App\Services\Server\Site\Repository\Providers
 */
class GitHub
{
    /**
     * Gets all the repositories for a user
     *
     * @param UserRepositoryProvider $userRepositoryProvider
     * @return mixed
     * @throws \Exception
     */
    public function getRepositories(UserRepositoryProvider $userRepositoryProvider)
    {
        $this->setToken($userRepositoryProvider);

        return GitHubService::api('repo')->all();
    }

    /**
     * Imports a deploy key so we can clone the repositories
     *
     * @param UserRepositoryProvider $userRepositoryProvider
     * @param $repository
     * @param $sshKey
     * @throws \Exception
     */
    public function importSshKey(UserRepositoryProvider $userRepositoryProvider, $repository, $sshKey)
    {
        $this->setToken($userRepositoryProvider);

        $repositoryInfo = $this->getRepositoryInfo($repository);

        if ($repositoryInfo['private']) {
            try {
                GitHubService::api('repo')->keys()->create(
                    $this->getRepositoryUser($repository),
                    $this->getRepositoryName($repository),
                    [
                        'title' => 'CodePier',
                        'key' => $sshKey,
                    ]
                );
            } catch (ValidationFailedException $e) {
                if (!$e->getMessage() == 'Validation Failed: key is already in use') {
                    throw new \Exception($e->getMessage());
                }
            }
        }
    }

    /**
     * Gets the repository information
     *
     * @param $repository
     * @return mixed
     */
    private function getRepositoryInfo($repository)
    {
        return GitHubService::api('repo')->show(
            $this->getRepositoryUser($repository),
            $this->getRepositoryName($repository)
        );
    }

    /**
     * Sets the token so we can connect to the users account
     * @param UserRepositoryProvider $userRepositoryProvider
     * @return mixed
     * @throws \Exception
     */
    private function setToken(UserRepositoryProvider $userRepositoryProvider)
    {
        return config(['github.connections.main.token' => $userRepositoryProvider->token]);
    }

    /**
     * Gets the users repositories username // TODO - move to a trait
     * @param $repository
     * @return mixed
     */
    private function getRepositoryUser($repository)
    {
        return explode('/', $repository)[0];
    }

    /**
     * Gets the users repositories name // TODO - move to a trait
     * @param $repository
     * @return mixed
     */
    private function getRepositoryName($repository)
    {
        return explode('/', $repository)[1];
    }
}