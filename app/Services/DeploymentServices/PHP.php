<?php

namespace App\Services\DeploymentServices;

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

    public function updateRepository()
    {
        dd('this is for non zero time deployments');
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

        $output[] = $this->remoteTaskService->run('([ -d '.$this->site_folder.'/storage ]) || (mv '.$this->release.'/storage '.$this->site_folder.')');
        $output[] = $this->remoteTaskService->run('rm '.$this->release.'/storage -rf');
        $output[] = $this->remoteTaskService->run('ln -s '.$this->site_folder.'/storage '.$this->release);

        // TODO - we need to add this to custom tasks, as not everyone will have a laravel or even PHP setup
        $output[] = $this->remoteTaskService->run('([ -f '.$this->site_folder.'/.env ]) || cat '.$this->release.'/.env.example >> '.$this->site_folder.'/.env');
        $output[] = $this->remoteTaskService->run('ln -s '.$this->site_folder.'/.env '.$this->release.'/.env');

        return $output;
    }

    /**
     * Install the vendors packages.
     */
    public function installPHPDependencies()
    {
        return $this->remoteTaskService->run('cd '.$this->release.'; composer install --no-interaction --no-ansi');
    }

    public function installNodeDependencies()
    {
        $output = [];
//
//        $output[] = $this->remoteTaskService->run('([ -d '.$this->site_folder.'/node_modules ]) || (cd '.$this->release.'; npm install --no-progress --production; mv '.$this->release.'/node_modules '.$this->site_folder.')');
//        $output[] = $this->remoteTaskService->run('ln -s '.$this->site_folder.'/node_modules '.$this->release);
//
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
     * Runs any migrations.
     */
    public function runMigrations()
    {
        $output = [];
        $output[] = $this->remoteTaskService->run('cd '.$this->release.'; php artisan migrate --force --no-interaction');
        $output[] = $this->remoteTaskService->run('cd '.$this->release.'; php artisan queue:restart');

        return $output;
    }

    /**
     * Cleans up the old deploys.
     */
    public function cleanup()
    {
        return $this->remoteTaskService->run('cd '.$this->site_folder.'; find . -maxdepth 1 -name "2*" -mmin +2880 | sort | head -n 10 | xargs rm -Rf');
    }
}
