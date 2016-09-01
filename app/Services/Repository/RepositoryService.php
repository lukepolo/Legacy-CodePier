<?php

namespace App\Services\Server\Site\Repository;

use App\Contracts\Server\Site\Repository\RepositoryServiceContract;
use App\Models\Site;
use App\Models\UserRepositoryProvider;

/**
 * Class RepositoryService.
 */
class RepositoryService implements RepositoryServiceContract
{
    protected $remoteTaskService;

    public $providers = [
        'github'    => Providers\GitHub::class,
        'gitlab'    => Providers\GitLab::class,
        'bitbucket' => Providers\BitBucket::class,
    ];

    /**
     * Imports a ssh key into the specific provider.
     *
     * @param Site $site
     *
     * @return mixed
     */
    public function importSshKeyIfPrivate(Site $site)
    {
        $providerService = $this->getProvider($site->userRepositoryProvider->repositoryProvider->provider_name);
        foreach ($site->servers as $server) {
            $providerService->importSshKeyIfPrivate(
                $site->userRepositoryProvider,
                $site->repository,
                $server->public_ssh_key
            );
        }
    }

    /**
     * Gets the provider that is passed in.
     *
     * @param $provider
     *
     * @return mixed
     */
    private function getProvider($provider)
    {
        // TODO - should have morph abilities
        return new $this->providers[$provider]();
    }

    public function getLatestCommit(UserRepositoryProvider $userRepositoryProvider, $repository, $branch)
    {
        return $this->getProvider($userRepositoryProvider->repositoryProvider->provider_name)->getLatestCommit($userRepositoryProvider,
            $repository, $branch);
    }

    public function createDeployHook(Site $site)
    {
        return $this->getProvider($site->userRepositoryProvider->repositoryProvider->provider_name)->createDeployHook($site);
    }

    public function deleteDeployHook(Site $site)
    {
        return $this->getProvider($site->userRepositoryProvider->repositoryProvider->provider_name)->deleteDeployHook($site);
    }
}
