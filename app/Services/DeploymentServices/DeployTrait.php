<?php

namespace App\Services\DeploymentServices;

use Carbon\Carbon;
use App\Models\Site\Site;
use App\Models\Server\Server;
use App\Models\Site\SiteDeployment;
use App\Services\RemoteTaskService;
use App\Services\Systems\SystemService;
use App\Services\Repository\RepositoryService;

trait DeployTrait
{
    public $release;
    public $releaseTime;
    public $rollback = false;

    private $site;
    private $branch;
    private $server;
    private $siteFolder;
    private $repository;
    private $remoteTaskService;
    private $repositoryService;
    private $repositoryProvider;
    private $zeroDowntimeDeployment;

    /**
     * @param RemoteTaskService $remoteTaskService
     * @param RepositoryService $repositoryService
     * @param Server $server
     * @param \App\Models\Site\Site $site
     * @param SiteDeployment $siteDeployment
     *
     * @throws \App\Exceptions\FailedCommand
     * @throws \App\Exceptions\SshConnectionFailed
     *
     * @throws \Exception
     */
    public function __construct(
        RemoteTaskService $remoteTaskService,
        RepositoryService $repositoryService,
        Server $server,
        Site $site,
        SiteDeployment $siteDeployment = null
    ) {
        $this->repositoryService = $repositoryService;

        $this->remoteTaskService = $remoteTaskService;

        $this->site = $site;
        $this->server = $server;
        $this->branch = $site->branch;
        $this->repository = $site->repository;
        $this->siteFolder = '/home/codepier/'.$site->domain;
        $this->zeroDowntimeDeployment = $site->zero_downtime_deployment;
        $this->releaseTime = Carbon::now()->format('YmdHis');

        if (! empty($siteDeployment)) {
            $this->remoteTaskService->ssh($server);
            if ($this->remoteTaskService->hasDirectory($this->siteFolder.'/'.$siteDeployment->folder_name)) {
                $this->rollback = true;
                $this->releaseTime = $siteDeployment->folder_name;
            } else {
                $this->sha = $siteDeployment->git_commit;
            }
        }

        $this->release = $this->siteFolder;

        if ($this->zeroDowntimeDeployment) {
            $this->release = $this->release.'/'.$this->releaseTime;
        }

        if ($site->userRepositoryProvider) {
            $this->repositoryProvider = $site->userRepositoryProvider->repositoryProvider;
        }
    }

    public function rollback()
    {
        $this->setupFolders();
    }

    /**
     * @description Updates the repository from the provider.
     *
     * @order 100
     *
     * @return array
     */
    public function cloneRepository()
    {
        $this->remoteTaskService->ssh($this->server, 'codepier');

        $output = [];

        $this->remoteTaskService->run('mkdir -p ' . $this->siteFolder);

        $loadSshKeyCommand = '';

        if ($this->repositoryProvider) {
            $token = $this->repositoryService->getToken($this->site->userRepositoryProvider);

            switch ($this->repositoryProvider->name) {
                case 'Bitbucket':
                    $url = "https://x-token-auth:{{$token}}@bitbucket.org/{$this->repository}.git";
                    break;
                case 'GitHub':
                    $url = "https://{$token}@github.com/{$this->repository}.git";
                    break;
                case 'GitLab':
                    $url = "https://oauth2:{$token}@gitlab.com/{$this->repository}.git";
                    break;
            }
        } else {
            $url = $this->repository;
            $loadSshKeyCommand = "GIT_SSH_COMMAND=\"ssh -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no -i '~/.ssh/{$this->site->id}_id_rsa'\"";
        }

        if ($this->zeroDowntimeDeployment) {
            $output[] = $this->remoteTaskService->run($loadSshKeyCommand . ' git clone ' . $url . ' --branch=' . $this->branch . (empty($this->sha) ? ' --depth=1' : '') . ' ' . $this->release);
        } else {
            if (! $this->remoteTaskService->hasDirectory($this->siteFolder.'/.git')) {
                $output[] = $this->remoteTaskService->run($loadSshKeyCommand . 'cd ' . $this->siteFolder . '; rm -rf * ;  git clone ' . $url . ' --branch=' . $this->branch . ' .');
            } else {
                $output[] = $this->remoteTaskService->run($loadSshKeyCommand . 'cd ' . $this->siteFolder . '; git pull origin ' . $this->branch);
            }
        }

        if (! empty($this->sha)) {
            $output[] = $this->remoteTaskService->run("cd $this->release; git reset --hard $this->sha");
        }

        return $output;
    }

    /**
     * @description Restarts Daemons
     *
     * @order 370
     *
     * @not_default
     */
    public function restartDaemons()
    {
        $this->remoteTaskService->ssh($this->server, 'root');

        return $this->remoteTaskService->run('/opt/codepier/./' . SystemService::DAEMON_PROGRAMS_GROUP);
    }

    /**
     * @description Restarts Workers
     *
     * @order 375
     *
     * @not_default
     */
    public function restartWorkers()
    {
        $this->remoteTaskService->ssh($this->server, 'root');

        return $this->remoteTaskService->run('/opt/codepier/./' . SystemService::WORKER_PROGRAMS_GROUP);
    }

    /**
     * @description Setups the folders for web service.
     *
     * @zero_downtime_deployment
     *
     * @order 400
     */
    public function setupFolders()
    {
        $this->remoteTaskService->ssh($this->server, 'codepier');

        if ($this->zeroDowntimeDeployment) {
            $currentFolder = $this->siteFolder.'/current';

            // Remove the docked with codepier index
            $this->remoteTaskService->run('ls -l ' . $currentFolder . ' | grep -v "\->" && rm -rf ' . $currentFolder . ' || echo true');

            return $this->remoteTaskService->run('ln -sfn ' . $this->release . ' ' . $currentFolder);
        }
    }

    /**
     * @description Cleans up the old deploys.
     *
     * @zero_downtime_deployment
     *
     * @order 600
     */
    public function cleanup()
    {
        if ($this->zeroDowntimeDeployment && $this->site->keep_releases > 0) {
            $this->remoteTaskService->ssh($this->server, 'root');

            return $this->remoteTaskService->run('cd ' . $this->siteFolder . '; find . -maxdepth 1 -name "2*" | sort -r | tail -n +' . ($this->site->keep_releases + 1) . ' | xargs rm -Rf');
        }
    }

    /**
     * Runs a custom step on the server.
     * @param $script
     * @return string
     */
    public function customStep($script)
    {
        $this->remoteTaskService->ssh($this->server, 'codepier');

        return $this->remoteTaskService->run('cd ' . $this->release . ' && ' . $script);
    }
}
