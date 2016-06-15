<?php

namespace App\Services\Server\Site\Repository;

use App\Contracts\Server\Site\Repository\RepositoryServiceContract;
use App\Models\Server;
use App\Models\User;

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
     * @param $provider
     * @param User $user
     * @return mixed
     */
    public function getRepositories($provider, User $user)
    {
        return $this->getProvider($provider)->getRepositories($user);
    }

    /**
     * Imports a ssh key into the specific provider
     * @param $service
     * @param User $user
     * @param $repository
     * @param $sshKey
     * @return mixed
     */
    public function importSshKey($service, User $user, $repository, $sshKey)
    {
        return $this->getProvider($service)->importSshKey($user, $repository, $sshKey);
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