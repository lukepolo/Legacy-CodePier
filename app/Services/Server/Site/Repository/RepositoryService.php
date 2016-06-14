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

    public function getRepositories($provider, User $user)
    {
        return $this->getProvider($provider)->getRepositories($user);
    }

    public function importSshKey(Server $server, User $user)
    {
        return $this->getProvider('github')->importSshKey($server, $user);
    }

    /**
     * @param $provider
     * @return mixed
     */
    private function getProvider($provider)
    {
        return new $this->providers[$provider]();
    }
}