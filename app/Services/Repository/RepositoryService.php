<?php

namespace App\Services\Repository;

use App\Models\Site\Site;
use App\Models\RepositoryProvider;
use App\Jobs\Server\InstallPublicKey;
use App\Exceptions\SshConnectionFailed;
use App\Models\User\UserRepositoryProvider;
use App\Exceptions\SiteUserProviderNotConnected;
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
     * @throws SiteUserProviderNotConnected
     */
    public function createDeployHook(Site $site)
    {
        if (! $site->userRepositoryProvider) {
            throw new SiteUserProviderNotConnected('You must check to see if the user provider is connected.');
        }

        return $this->getProvider($site->userRepositoryProvider->repositoryProvider)->createDeployHook($site);
    }

    /**
     * @param Site $site
     * @return Site $site
     * @throws SiteUserProviderNotConnected
     */
    public function deleteDeployHook(Site $site)
    {
        if (! $site->userRepositoryProvider) {
            throw new SiteUserProviderNotConnected('You must check to see if the user provider is connected.');
        }

        return $this->getProvider($site->userRepositoryProvider->repositoryProvider)->deleteDeployHook($site);
    }

    /**
     * @param UserRepositoryProvider $userRepositoryProvider
     * @return mixed
     */
    public function getToken(UserRepositoryProvider $userRepositoryProvider)
    {
        return $this->getProvider($userRepositoryProvider->repositoryProvider)->getToken($userRepositoryProvider);
    }

    /**
     * Generates keys based for the site.
     * @param Site $site
     */
    public function generateNewSshKeys(Site $site)
    {
        $sshKey = $this->remoteTaskService->createSshKey();
        $site->public_ssh_key = $sshKey['publickey'];
        $site->private_ssh_key = $sshKey['privatekey'];
        $site->save();

        $this->saveKeysToServer($site);
    }

    /**
     * Saves the public keys to the server.
     * @param Site $site
     */
    public function saveKeysToServer(Site $site)
    {
        foreach ($site->provisionedServers as $server) {
            try {
                dispatch(
                    (new InstallPublicKey($server, $site))
                        ->onQueue(config('queue.channels.server_commands'))
                );
            } catch (SshConnectionFailed $sshConnectionFailed) {
                continue;
            }
        }
    }
}
