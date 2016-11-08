<?php

namespace App\Services\DeploymentServices\PHP;

use App\Models\Server\Server;
use App\Models\Site\Site;
use App\Services\RemoteTaskService;
use Carbon\Carbon;

class PHP
{
    private $branch;
    private $release;
    private $repository;
    private $remoteTaskService;
    private $repositoryProvider;

    /**
     * @param RemoteTaskService $remoteTaskService
     * @param Server            $server
     * @param \App\Models\Site\Site              $site
     */
    public function __construct(RemoteTaskService $remoteTaskService, Server $server, Site $site)
    {
        $this->remoteTaskService = $remoteTaskService;
        $this->remoteTaskService->ssh($server, 'codepier', true);

        $this->branch = $site->branch;
        $this->repository = $site->repository;
        $this->site_folder = '/home/codepier/'.$site->domain;
        $this->zerotimeDeployment = $site->zerotime_deployment;
        $this->release = $this->site_folder.'/'.Carbon::now()->format('YmdHis');

        $this->repositoryProvider = $site->userRepositoryProvider->repositoryProvider;
    }

    /**
     * Updates the repository from the provider.
     * @param null $sha
     * @return array
     */
    public function cloneRepository($sha = null)
    {
        $output = [];

        $output[] = $this->remoteTaskService->run('mkdir -p '.$this->site_folder);
        $output[] = $this->remoteTaskService->run('ssh-keyscan -t rsa '.$this->repositoryProvider->url.' | tee -a ~/.ssh/known_hosts');

        $output[] = $this->remoteTaskService->run('eval `ssh-agent -s` > /dev/null 2>&1; ssh-add ~/.ssh/id_rsa > /dev/null 2>&1 ; cd '.$this->site_folder.'; git clone '.$this->repositoryProvider->git_url.':'.$this->repository.' --branch='.$this->branch.(empty($sha) ? ' --depth=1 ' : ' ').$this->release);

        if (! empty($sha)) {
            $output[] = $this->remoteTaskService->run("cd $this->release; git reset --hard $sha");
        }

        return $output;
    }

    /**
     * Install the vendors packages.
     */
    public function installPHPDependencies()
    {
        return $this->remoteTaskService->run('cd '.$this->release.'; composer install --no-progress --no-interaction --no-dev --prefer-dist');
    }

    public function installNodeDependencies()
    {
        $output = [];

        $output[] = $this->remoteTaskService->run('([ -d '.$this->site_folder.'/node_modules ]) || (cd '.$this->release.'; yarn install --silent --no-progress --production; mv '.$this->release.'/node_modules '.$this->site_folder.')');
        $output[] = $this->remoteTaskService->run('ln -s '.$this->site_folder.'/node_modules '.$this->release);

        return $output;
    }

    /**
     *  Setups the folders for web service.
     */
    public function setupFolders()
    {
        return $this->remoteTaskService->run('ln -sfn '.$this->release.' '.$this->site_folder.($this->zerotimeDeployment ? '/current' : null));
    }

    /**
     * Cleans up the old deploys.
     */
    public function cleanup()
    {
        return $this->remoteTaskService->run('cd '.$this->site_folder.'; find . -maxdepth 1 -name "2*" -mmin +2880 | sort | head -n 10 | xargs rm -Rf');
    }
}
