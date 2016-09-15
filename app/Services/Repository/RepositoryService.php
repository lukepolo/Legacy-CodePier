<?php

namespace App\Services\Repository;

use App\Contracts\Repository\RepositoryServiceContract;
use App\Models\RepositoryProvider;
use App\Models\Site;
use App\Models\UserRepositoryProvider;

/**
 * Class RepositoryService.
 */
class RepositoryService implements RepositoryServiceContract
{
    protected $remoteTaskService;

    /**
     * Imports a ssh key into the specific provider.
     *
     * @param Site $site
     *
     * @return mixed
     */
    public function importSshKeyIfPrivate(Site $site)
    {
        $providerService = $this->getProvider($site->userRepositoryProvider->repositoryProvider);
        foreach ($site->provisionedServers as $server) {
            $providerService->importSshKeyIfPrivate(
                $site->userRepositoryProvider,
                $site->repository,
                $server->public_ssh_key
            );
        }
    }

    /**
     * @param RepositoryProvider $repositoryProvider
     * @return mixed
     */
    private function getProvider(RepositoryProvider $repositoryProvider)
    {
        return new $repositoryProvider->repository_class();
    }

    public function getLatestCommit(UserRepositoryProvider $userRepositoryProvider, $repository, $branch)
    {
        return $this->getProvider($userRepositoryProvider->repositoryProvider)->getLatestCommit($userRepositoryProvider,
            $repository, $branch);
    }

    public function createDeployHook(Site $site)
    {
        return $this->getProvider($site->userRepositoryProvider->repositoryProvider)->createDeployHook($site);
    }

    public function deleteDeployHook(Site $site)
    {
        return $this->getProvider($site->userRepositoryProvider->repositoryProvider)->deleteDeployHook($site);
    }
}
