<?php

namespace App\Services\Repository;

use App\Models\Site\Site;
use App\Models\RepositoryProvider;
use App\Jobs\Server\InstallPublicKey;
use App\Exceptions\SshConnectionFailed;
use App\Exceptions\DeployKeyAlreadyUsed;
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
     * Imports a ssh key into the specific provider.
     *
     * @param Site $site
     * @return mixed
     * @throws SiteUserProviderNotConnected
     * @throws \Exception
     */
    public function importSshKey(Site $site)
    {
        if (! $site->userRepositoryProvider) {
            throw new SiteUserProviderNotConnected('You must check to see if the user provider is connected.');
        }

        $providerService = $this->getProvider($site->userRepositoryProvider->repositoryProvider);

        if (empty($site->public_ssh_key) || empty($site->private_ssh_key)) {
            $this->generateNewSshKeys($site);
        }

        // We only import to github if we haven't said its private
        if (! $site->private && $this->isPrivate($site)) {
            \Log::info('we should be importing');

            try {
                $providerService->importSshKey($site);
            } catch (\Exception $e) {
                if ($e instanceof DeployKeyAlreadyUsed) {
                    $this->importSshKey($site);
                } else {
                    $site->update([
                        'public_ssh_key' => null,
                    ]);

                    throw $e;
                }
            }
        }
    }

    /**
     * @param Site $site
     * @return mixed
     * @throws SiteUserProviderNotConnected
     */
    public function isPrivate(Site $site)
    {
        if (! $site->userRepositoryProvider) {
            throw new SiteUserProviderNotConnected('You must check to see if the user provider is connected.');
        }

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
                    (
                        new InstallPublicKey($server, $site)
                    )->onQueue(config('queue.channels.server_commands'))
                );
            } catch (SshConnectionFailed $sshConnectionFailed) {
                continue;
            }
        }
    }
}
