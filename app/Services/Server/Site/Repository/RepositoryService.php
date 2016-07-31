<?php

namespace App\Services\Server\Site\Repository;

use App\Contracts\Server\Site\Repository\RepositoryServiceContract;
use App\Models\Site;
use App\Models\UserRepositoryProvider;

/**
 * Class RepositoryService
 * @package App\Services
 */
class RepositoryService implements RepositoryServiceContract
{

    protected $remoteTaskService;

    public $providers = [
        'github' => Providers\GitHub::class,
        'bitbucket' => Providers\BitBucket::class
    ];

    /**
     * Imports a ssh key into the specific provider
     * @param UserRepositoryProvider $userRepositoryProvider
     * @param $repository
     * @param $sshKey
     * @return mixed
     */
    public function importSshKeyIfPrivate(UserRepositoryProvider $userRepositoryProvider, $repository, $sshKey)
    {
        return $this->getProvider($userRepositoryProvider->repositoryProvider->provider_name)->importSshKeyIfPrivate($userRepositoryProvider, $repository, $sshKey);
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

    public function getLatestCommit(UserRepositoryProvider $userRepositoryProvider, $repository, $branch)
    {
        return $this->getProvider($userRepositoryProvider->repositoryProvider->provider_name)->getLatestCommit($userRepositoryProvider, $repository, $branch);
    }

    public function createDeployHook(Site $site)
    {
        return $this->getProvider($site->userRepositoryProvider->repositoryProvider->provider_name)->createDeployHook($site);
    }
}