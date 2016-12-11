<?php

namespace App\Services\DeploymentServices\PHP;

use Carbon\Carbon;
use App\Models\Site\Site;
use App\Models\Server\Server;
use App\Services\RemoteTaskService;
use App\Services\Systems\SystemService;
use App\Services\DeploymentServices\PHP\Frameworks\Laravel;

class PHP
{
    use Laravel;

    private $site;
    private $branch;
    private $server;
    private $release;
    private $site_folder;
    private $repository;
    private $remoteTaskService;
    private $repositoryProvider;
    private $zerotimeDeployment;

    /**
     * @param RemoteTaskService $remoteTaskService
     * @param Server            $server
     * @param \App\Models\Site\Site              $site
     */
    public function __construct(RemoteTaskService $remoteTaskService, Server $server, Site $site)
    {
        $this->remoteTaskService = $remoteTaskService;
        $this->remoteTaskService->ssh($server, 'codepier');

        $this->site = $site;
        $this->server = $server;
        $this->branch = $site->branch;
        $this->repository = $site->repository;
        $this->site_folder = '/home/codepier/'.$site->domain;
        $this->zerotimeDeployment = $site->zerotime_deployment;
        $this->release = $this->site_folder.'/'.Carbon::now()->format('YmdHis');

        $this->repositoryProvider = $site->userRepositoryProvider->repositoryProvider;
    }

    /**
     * @description Updates the repository from the provider.
     *
     * @order 100
     *
     * @param null $sha
     * @return array
     */
    public function cloneRepository($sha = null)
    {
        $output = [];

        $this->remoteTaskService->run('mkdir -p '.$this->site_folder);
        $this->remoteTaskService->run('ssh-keyscan -t rsa '.$this->repositoryProvider->url.' | tee -a ~/.ssh/known_hosts');

        $url = 'git://'.$this->repositoryProvider->url.'/'.$this->repository.'.git';

        if($this->site->private) {
            $url = $this->repositoryProvider->git_url.':'.$this->repository;
        }

        $output[] = $this->remoteTaskService->run('eval `ssh-agent -s` > /dev/null 2>&1; ssh-add ~/.ssh/id_rsa > /dev/null 2>&1 ; cd '.$this->site_folder.'; git clone '.$url.' --branch='.$this->branch.(empty($sha) ? ' --depth=1 ' : ' ').$this->release);

        if (! empty($sha)) {
            $output[] = $this->remoteTaskService->run("cd $this->release; git reset --hard $sha");
        }

        return $output;
    }

    /**
     * @description Install the vendors packages.
     *
     * @order 200
     */
    public function installPhpDependencies()
    {
        return [$this->remoteTaskService->run('cd '.$this->release.'; composer install --no-progress --no-interaction --no-dev --prefer-dist')];
    }

    /**
     * @description Install the node vendors packages.
     *
     * @order 300
     */
    public function installNodeDependencies()
    {
        $output = [];

        $output[] = $this->remoteTaskService->run('([ -d '.$this->site_folder.'/node_modules ]) || (cd '.$this->release.'; yarn install --no-progress --production; mv '.$this->release.'/node_modules '.$this->site_folder.')');
        $output[] = $this->remoteTaskService->run('ln -s '.$this->site_folder.'/node_modules '.$this->release);

        return $output;
    }

    /**
     * @description Setups the folders for web service.
     *
     * @order 400
     */
    public function setupFolders()
    {
        return [$this->remoteTaskService->run('ln -sfn '.$this->release.' '.$this->site_folder.($this->zerotimeDeployment ? '/current' : null))];
    }

    /**
     * @description Cleans up the old deploys.
     *
     * @order 500
     */
    public function cleanup()
    {
        return [$this->remoteTaskService->run('cd '.$this->site_folder.'; find . -maxdepth 1 -name "2*" -mmin +2880 | sort | head -n 10 | xargs rm -Rf')];
    }

    /**
     * @description Restart the web services
     *
     * @order 600
     */
    public function restartWebServices()
    {
        $this->remoteTaskService->ssh($this->server, 'root');

        return [$this->remoteTaskService->run('/opt/codepier/./'.SystemService::WEB_SERVICE_GROUP)];
    }
}
