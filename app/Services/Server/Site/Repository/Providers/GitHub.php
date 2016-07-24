<?php

namespace App\Services\Server\Site\Repository\Providers;

use App\Models\UserRepositoryProvider;
use GitHub as GitHubService;
use Github\Exception\ValidationFailedException;

/**
 * Class GitHub
 * @package App\Services\Server\Site\Repository\Providers
 */
class GitHub implements RepositoryContract
{
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

        if($this->isRepositoryPrivate($repository)) {
            try {
                GitHubService::api('repo')->keys()->create(
                    $this->getRepositoryUser($repository),
                    $this->getRepositorySlug($repository),
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

    public function isRepositoryPrivate($repository)
    {
        $repositoryInfo = $this->getRepositoryInfo($repository);

        if(isset($repositoryInfo['private'])) {
            return $repositoryInfo['private'];
        }
        return false;
    }

    /**
     * Gets the repository information
     *
     * @param $repository
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
     * Sets the token so we can connect to the users account
     * @param UserRepositoryProvider $userRepositoryProvider
     * @return mixed
     * @throws \Exception
     */
    public function setToken(UserRepositoryProvider $userRepositoryProvider)
    {
        return config(['github.connections.main.token' => $userRepositoryProvider->token]);
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
}