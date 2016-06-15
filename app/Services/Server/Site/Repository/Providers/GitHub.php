<?php

namespace App\Services\Server\Site\Repository\Providers;

use App\Models\User;
use GitHub as GitHubService;
use Github\Exception\ValidationFailedException;

class GitHub
{
    public function getRepositories(User $user)
    {
        $this->setToken($user);

        return GitHubService::api('repo')->all();
    }

    public function importSshKey(User $user, $repository, $sshKey)
    {
        $this->setToken($user);

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

    private function getRepositoryInfo($repository)
    {
        return GitHubService::api('repo')->show(
            $this->getRepositoryUser($repository),
            $this->getRepositoryName($repository)
        );
    }

    private function setToken(User $user)
    {
        if ($userRepositoryProvider = $user->userRepositoryProviders->where('service', 'github')->first()) {
            return config(['github.connections.main.token' => $userRepositoryProvider->token]);
        }

        throw new \Exception('No server provider found for this user');
    }

    private function getRepositoryUser($repository)
    {
        return explode('/', $repository)[0];
    }

    private function getRepositoryName($repository)
    {
        return explode('/', $repository)[1];
    }
}