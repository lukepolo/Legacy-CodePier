<?php

namespace App\Services\Server\Site\DeploymentServices;

use App\Models\Server;
use App\Models\Site;
use App\Services\RemoteTaskService;
use Carbon\Carbon;

/**
 * Class PHP
 * @package App\Services\Server\Site\DeploymentServices
 */
class PHP
{
    private $branch;
    private $release;
    private $repository;
    private $remoteTaskService;
    private $repositoryProvider;

    /**
     * @param RemoteTaskService $remoteTaskService
     * @param Server $server
     * @param Site $site
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
     * Updates the repository from the provider
     */
    public function cloneRepository($sha = null)
    {
        $output = [];

        $output[] = $this->remoteTaskService->run('mkdir -p ' . $this->site_folder);
        $output[] = $this->remoteTaskService->run('ssh-keyscan -H '.$this->repositoryProvider->url.' >> ~/.ssh/known_hosts > /dev/null 2>&1');

        $output[] = $this->remoteTaskService->run('eval `ssh-agent -s` > /dev/null 2>&1; ssh-add ~/.ssh/id_rsa > /dev/null 2>&1 ; cd ' . $this->site_folder . '; git clone '.$this->repositoryProvider->git_url.':' . $this->repository . ' --branch=' . $this->branch . (empty($sha) ? ' --depth=1 ' : ' ') . $this->release);

        if(!empty($sha)) {
            $output[] = $this->remoteTaskService->run("cd $this->release; git reset --hard $sha");
        }

        $output[] = $this->remoteTaskService->run('([ -d ' . $this->site_folder . '/public ]) || (mv ' . $this->release . '/storage ' . $this->site_folder.')');
        $output[] = $this->remoteTaskService->run('rsyn -au '.$this->site_folder . '/storage '.$this->site_folder.'/storage; rm '.$this->site_folder.' -rf');

        // TODO - test to make sure this works , public directory should not be sym linked
        $output[] = $this->remoteTaskService->run('ln -s ' . $this->site_folder . '/storage ' . $this->release . '/storage');

        $output[] = $this->remoteTaskService->run('([ -f ' . $this->site_folder . '/.env ]) || echo >> ' . $this->site_folder . '/.env');
        $output[] = $this->remoteTaskService->run('ln -s ' . $this->site_folder . '/.env ' . $this->release . '/.env');

        return $output;
    }

    /**
     * Install the vendors packages
     */
    public function installPHPDependencies()
    {
        return $this->remoteTaskService->run('cd ' . $this->release . '; composer install --no-interaction --no-ansi');
    }

    public function installNodeDependencies()
    {
        $output = [];

        $output[] = $this->remoteTaskService->run('([ -d ' . $this->site_folder . '/node_modules ]) || (cd ' . $this->release . '; npm install --no-progress --production; mv ' . $this->release . '/node_modules ' . $this->site_folder.')');
        $output[] = $this->remoteTaskService->run('ln -s ' . $this->site_folder . '/node_modules ' . $this->release . '/node_modules');

        return $output;
    }

    /**
     *  Setups the folders for web service
     */
    public function setupFolders()
    {
        return $this->remoteTaskService->run('ln -sfn ' . $this->release . ' ' . $this->site_folder . ($this->zerotimeDeployment ? '/current' : null ));
    }

    /**
     * Runs any migrations
     */
    public function runMigrations()
    {
        $output = [];
        $output[] = $this->remoteTaskService->run('cd ' . $this->release . '; php artisan migrate --force --no-interaction');
        $output[] = $this->remoteTaskService->run('cd ' . $this->release . '; php artisan queue:restart');

        return $output;
    }

    /**
     * Cleans up the old deploys
     */
    public function cleanup()
    {
        return $this->remoteTaskService->run('cd ' . $this->site_folder . '; find . -maxdepth 1 -name "2*" -mmin +2880 | sort | head -n 10 | xargs rm -Rf');
    }
}