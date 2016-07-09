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
    private $path;
    private $branch;
    private $release;
    private $repository;
    private $remoteTaskService;

    /**
     * ProvisionService constructor.
     * @param RemoteTaskService $remoteTaskService
     * @param Server $server
     */
    public function __construct(RemoteTaskService $remoteTaskService, Server $server, Site $site)
    {
        $this->remoteTaskService = $remoteTaskService;
        $this->remoteTaskService->ssh($server->ip, 'codepier');

        $this->path = $site->path;
        $this->branch = $site->branch;
        $this->repository = $site->repository;
        $this->release = Carbon::now()->format('YmdHis');
    }

    /**
     * Updates the repository from the provider
     */
    public function updateRepository()
    {
        $this->remoteTaskService->run('mkdir -p ' . $this->path);
        $this->remoteTaskService->run('ssh-keyscan -H github.com >> ~/.ssh/known_hosts');

        $this->remoteTaskService->run('eval `ssh-agent -s`; ssh-add ~/.ssh/id_rs; cd ' . $this->path . '; git clone git@github.com:' . $this->repository . ' --branch=' . $this->branch . ' --depth=1 ' . $this->release);
        $this->remoteTaskService->run('cd ' . $this->path . '/' . $this->release . '; echo "*" > .git/info/sparse-checkout');
        $this->remoteTaskService->run('cd ' . $this->path . '/' . $this->release . '; echo "!storage" >> .git/info/sparse-checkout');
        $this->remoteTaskService->run('cd ' . $this->path . '/' . $this->release . '; echo "!public/build" >> .git/info/sparse-checkout');

        // TODO - having issues with this
        $this->remoteTaskService->run('[ -f ' . $this->path . '.env ] && echo "Found" || cp ' . $this->path . '/' . $this->release . '/.env.example ' . $this->path . '/.env; cd ' . $this->path . '/' . $this->release);

        $this->remoteTaskService->run('ln -s ' . $this->path . '/.env ' . $this->path . '/' . $this->release . '/.env');
    }

    /**
     * Install the vendors packages
     */
    public function installVendorPackages()
    {
        $this->remoteTaskService->run('cd ' . $this->path . '/' . $this->release . '; composer install --no-interaction');

        $this->remoteTaskService->run('[ -f ' . $this->path . '/node_modules ] && echo "Found" || cd ' . $this->path . '/' . $this->release . '; npm install --production; mv ' . $this->path . '/' . $this->release . '/node_modules ' . $this->path . '/node_modules');

        $this->remoteTaskService->run('ln -s ' . $this->path . '/node_modules ' . $this->path . '/' . $this->release . '/node_modules');
    }

    /**
     *  Setups the folders for web service
     */
    public function setupFolders()
    {
        $this->remoteTaskService->run('ln -sfn ' . $this->path . '/' . $this->release . ' ' . $this->path . '/current');
    }

    /**
     * Runs any migrations
     */
    public function runMigrations()
    {
        $this->remoteTaskService->run('cd ' . $this->path . '/' . $this->release . '; php artisan migrate --force --no-interaction; php artisan queue:restart');
    }

    /**
     * Cleans up the old deploys
     */
    public function cleanup()
    {
        $this->remoteTaskService->run('cd ' . $this->path . '; find . -maxdepth 1 -name "2*" -mmin +2880 | sort | head -n 10 | xargs rm -Rf');
    }
}