<?php

namespace App\Services\Repository;

use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;
use App\Exceptions\DeployKeyAlreadyUsed;
use App\Models\Site\Site;
use App\Models\RepositoryProvider;
use App\Models\User\UserRepositoryProvider;
use App\Contracts\Repository\RepositoryServiceContract;

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
     *
     * @return mixed
     */
    public function importSshKeyIfPrivate(Site $site)
    {
        $providerService = $this->getProvider($site->userRepositoryProvider->repositoryProvider);

        if(empty($site->public_ssh_key)) {
            $this->generateNewSshKeys($site);
        }

        try {
            $providerService->importSshKeyIfPrivate($site);
        } catch(DeployKeyAlreadyUsed $e) {
            $this->generateNewSshKeys($site);
            $providerService->importSshKeyIfPrivate($site);
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

    /**
     * @param UserRepositoryProvider $userRepositoryProvider
     * @param $repository
     * @param $branch
     * @return mixed
     */
    public function getLatestCommit(UserRepositoryProvider $userRepositoryProvider, $repository, $branch)
    {
        return $this->getProvider($userRepositoryProvider->repositoryProvider)->getLatestCommit($userRepositoryProvider,
            $repository, $branch);
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
     * Generates keys based for the site
     * @param Site $site
     */
    private function generateNewSshKeys(Site $site)
    {
        $sshKey = $this->remoteTaskService->createSshKey();
        $site->public_ssh_key = $sshKey['publickey'];
        $site->private_ssh_key = $sshKey['privatekey'];
        $site->save();

        $sshFile = '~/.ssh/'.$site->id.'_id_rsa';

        foreach ($site->provisionedServers as $server) {

            $this->remoteTaskService->ssh($server, 'codepier');

            $this->remoteTaskService->writeToFile($sshFile, $site->private_ssh_key);
            $this->remoteTaskService->writeToFile($sshFile.'.pub', $site->public_ssh_key);

            $this->remoteTaskService->appendTextToFile("~/.ssh/config", "IdentityFile $sshFile");

            \Log::info("$sshFile");
        }
    }
}
