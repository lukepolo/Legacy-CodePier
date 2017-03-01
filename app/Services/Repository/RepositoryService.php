<?php

namespace App\Services\Repository;

use App\Models\Site\Site;
use App\Models\RepositoryProvider;
use App\Exceptions\SshConnectionFailed;
use App\Exceptions\DeployKeyAlreadyUsed;
use App\Contracts\Repository\RepositoryServiceContract;
use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;

class RepositoryService implements RepositoryServiceContract
{
    protected $remoteTaskService;

    /**
     * RepositoryService constructor.
     * @param \App\Services\RemoteTaskService | RemoteTaskService $remoteTaskService
     */
    public function __construct(RemoteTaskService $remoteTaskService)
    {
        $this->remoteTaskService = $remoteTaskService;
    }

    /**
     * Imports a ssh key into the specific provider.
     *
     * @param Site $site
     * @return mixed
     * @throws \Exception
     */
    public function importSshKey(Site $site)
    {
        $providerService = $this->getProvider($site->userRepositoryProvider->repositoryProvider);

        if (empty($site->public_ssh_key) || empty($site->private_ssh_key)) {
            $this->isPrivate($site);
            $this->generateNewSshKeys($site);

            try {
                $providerService->importSshKey($site);
            } catch (DeployKeyAlreadyUsed $e) {
                $this->importSshKey($site);
            }

            foreach ($site->provisionedServers as $server) {
                try {
                    $this->remoteTaskService->saveSshKeyToServer($site, $server);
                } catch (SshConnectionFailed $sshConnectionFailed) {
                    continue;
                }
            }
        }
    }

    /**
     * @param Site $site
     * @return mixed
     */
    public function isPrivate(Site $site)
    {
        $providerService = $this->getProvider($site->userRepositoryProvider->repositoryProvider);
        $private = $providerService->isPrivate($site);

        if ($site->private != $private) {
            $site->update([
                'private' => $private,
            ]);
        }

        return $private;
    }

    /**
     * @param RepositoryProvider $repositoryProvider
     * @return mixed
     */
    private function getProvider(RepositoryProvider $repositoryProvider)
    {
        return new $repositoryProvider->repository_class();
    }

    /**
     * @param Site $site
     * @return Site $site
     */
    public function createDeployHook(Site $site)
    {
        return $this->getProvider($site->userRepositoryProvider->repositoryProvider)->createDeployHook($site);
    }

    /**
     * @param Site $site
     * @return Site $site
     */
    public function deleteDeployHook(Site $site)
    {
        return $this->getProvider($site->userRepositoryProvider->repositoryProvider)->deleteDeployHook($site);
    }

    /**
     * Generates keys based for the site.
     * @param Site $site
     */
    private function generateNewSshKeys(Site $site)
    {
        $sshKey = $this->remoteTaskService->createSshKey();
        $site->public_ssh_key = $sshKey['publickey'];
        $site->private_ssh_key = $sshKey['privatekey'];
        $site->save();
    }
}
