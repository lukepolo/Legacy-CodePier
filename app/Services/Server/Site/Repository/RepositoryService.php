<?php

namespace App\Services\Server\Site\Repository;

use App\Contracts\Server\Site\Repository\RepositoryServiceContract;
use App\Models\User;
use App\Models\UserRepositoryProvider;

/**
 * Class RepositoryService
 * @package App\Services
 */
class RepositoryService implements RepositoryServiceContract
{

    protected $remoteTaskService;

    public $providers = [
        'github' => Providers\GitHub::class
    ];

    /**
     * Gets a users repositires from the specific provider
     *
     * @param User $user
     * @return mixed
     */
    public function getUserRepositories(User $user)
    {
        $repositories = [];

        foreach ($user->userRepositoryProviders as $repositoryProvider) {
            $providerName = $repositoryProvider->repositoryProvider->provider_name;
            $repositories[$providerName] = $this->getProvider($providerName)->getRepositories($repositoryProvider);
        }

        return $repositories;
    }

    /**
     * Imports a ssh key into the specific provider
     * @param UserRepositoryProvider $userRepositoryProvider
     * @param $repository
     * @param $sshKey
     * @return mixed
     */
    public function importSshKey(UserRepositoryProvider $userRepositoryProvider, $repository, $sshKey)
    {
        return $this->getProvider($userRepositoryProvider->provider_name)->importSshKey($userRepositoryProvider,
            $repository, $sshKey);
    }

    /**
     * Gets the provider that is passed in
     *
     * @param $provider
     * @return mixed
     */
    private function getProvider($provider)
    {
        return new $this->providers[$provider]();
    }
}