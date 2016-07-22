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

    /**
     * ProvisionService constructor.
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
    }

    public function updateRepository()
    {
        dd('this is for non zero time deployments');
    }

    /**
     * Updates the repository from the provider
     */
    public function cloneRepository()
    {
        $this->remoteTaskService->run('mkdir -p ' . $this->site_folder);
        $this->remoteTaskService->run('ssh-keyscan -H github.com >> ~/.ssh/known_hosts > /dev/null 2>&1');

        $this->remoteTaskService->run('eval `ssh-agent -s`; ssh-add ~/.ssh/id_rsa; cd ' . $this->site_folder . '; git clone git@github.com:' . $this->repository . ' --branch=' . $this->branch . ' --depth=1 ' . $this->release);

        $this->remoteTaskService->run('cd ' . $this->release . '; echo "*" > .git/info/sparse-checkout');
        $this->remoteTaskService->run('cd ' . $this->release . '; echo "!storage" >> .git/info/sparse-checkout');
        $this->remoteTaskService->run('cd ' .$this->release . '; echo "!public/build" >> .git/info/sparse-checkout');

        $this->remoteTaskService->run('([ -f ' . $this->site_folder . '/.env ] && echo "Found") || cp ' . $this->release . '/.env.example ' . $this->site_folder . '/.env; cd ' . $this->release);
        $this->remoteTaskService->run('ln -s ' . $this->site_folder . '/.env ' . $this->release . '/.env');
    }

    /**
     * Install the vendors packages
     */
    public function installPHPDependencies()
    {
        $this->remoteTaskService->run('cd ' . $this->release . '; composer install --no-interaction --no-ansi --no-progress --quiet');
    }

    public function installNodeDependencies()
    {
        $this->remoteTaskService->run('([ -d ' . $this->site_folder . '/node_modules ] && echo "Found") || cd ' . $this->release . '; npm install --production; mv ' . $this->release . '/node_modules ' . $this->site_folder);

        // TODO - if they want they can have theire node modules updated
        $this->remoteTaskService->run('ln -s ' . $this->site_folder . '/node_modules ' . $this->release . '/node_modules');
    }

    /**
     *  Setups the folders for web service
     */
    public function setupFolders()
    {
        $this->remoteTaskService->run('ln -sfn ' . $this->release . ' ' . $this->site_folder . ($this->zerotimeDeployment ? '/current' : null ));
    }

    /**
     * Runs any migrations
     */
    public function runMigrations()
    {
        $this->remoteTaskService->run('cd ' . $this->release . '; php artisan migrate --force --no-interaction; php artisan queue:restart');
    }

    /**
     * Cleans up the old deploys
     */
    public function cleanup()
    {
        $this->remoteTaskService->run('cd ' . $this->site_folder . '; find . -maxdepth 1 -name "2*" -mmin +2880 | sort | head -n 10 | xargs rm -Rf');
    }
}