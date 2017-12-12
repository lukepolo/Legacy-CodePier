<?php

namespace App\Services\DeploymentServices;

use Carbon\Carbon;
use App\Models\Site\Site;
use App\Models\Server\Server;
use App\Models\Site\SiteDeployment;
use App\Services\RemoteTaskService;

trait DeployTrait
{
    public $release;
    public $releaseTime;

    private $site;
    private $branch;
    private $server;
    private $siteFolder;
    private $repository;
    private $rollback = false;
    private $remoteTaskService;
    private $repositoryProvider;
    private $zerotimeDeployment;

    /**
     * @param RemoteTaskService $remoteTaskService
     * @param Server $server
     * @param \App\Models\Site\Site $site
     * @param SiteDeployment $siteDeployment
     * @throws \App\Exceptions\SshConnectionFailed
     */
    public function __construct(RemoteTaskService $remoteTaskService, Server $server, Site $site, SiteDeployment $siteDeployment = null)
    {
        $this->remoteTaskService = $remoteTaskService;
        $this->remoteTaskService->ssh($server, 'codepier');

        $this->site = $site;
        $this->server = $server;
        $this->branch = $site->branch;
        $this->repository = $site->repository;
        $this->siteFolder = '/home/codepier/'.$site->domain;
        $this->zerotimeDeployment = $site->zerotime_deployment;
        $this->releaseTime = Carbon::now()->format('YmdHis');

        if (! empty($siteDeployment)) {
            if ($this->remoteTaskService->hasDirectory($this->siteFolder.'/'.$siteDeployment->folder_name)) {
                $this->rollback = true;
                $this->releaseTime = $siteDeployment->folder_name;
            } else {
                $this->sha = $siteDeployment->git_commit;
            }
        }

        $this->release = $this->siteFolder;

        if ($this->zerotimeDeployment) {
            $this->release = $this->release.'/'.$this->releaseTime;
        }

        if ($site->userRepositoryProvider) {
            $this->repositoryProvider = $site->userRepositoryProvider->repositoryProvider;
        }
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
        $output = [];

        if (! $this->rollback) {
            $this->remoteTaskService->run('mkdir -p '.$this->siteFolder);

            if ($this->repositoryProvider) {
                $host = $this->repositoryProvider->url;
                $url = 'https://'.$host.'/'.$this->repository;

                if ($this->site->private) {
                    $url = $this->repositoryProvider->git_url.':'.$this->repository;
                }
            } else {
                $repositoryUrl = parse_url($this->repository);
                $host = $repositoryUrl['host'];
                $url = 'git@'.$repositoryUrl['host'].':'.trim($repositoryUrl['path'], '/');
            }

            $this->remoteTaskService->run('ssh-keyscan -t rsa '.$host.' | tee -a ~/.ssh/known_hosts');

            if ($this->zerotimeDeployment) {
                $output[] = $this->remoteTaskService->run('eval `ssh-agent -s` > /dev/null 2>&1; ssh-add ~/.ssh/'.$this->site->id.'_id_rsa > /dev/null 2>&1 ; cd '.$this->siteFolder.'; git clone '.$url.' --branch='.$this->branch.(empty($this->sha) ? ' --depth=1' : '').' '.$this->release);
            } else {
                if (! $this->remoteTaskService->hasDirectory($this->siteFolder.'/.git')) {
                    $output[] = $this->remoteTaskService->run('eval `ssh-agent -s` > /dev/null 2>&1; ssh-add ~/.ssh/'.$this->site->id.'_id_rsa > /dev/null 2>&1 ; cd '.$this->siteFolder.'; rm -rf * ;  git clone '.$url.' --branch='.$this->branch.' .');
                } else {
                    $output[] = $this->remoteTaskService->run('eval `ssh-agent -s` > /dev/null 2>&1; ssh-add ~/.ssh/'.$this->site->id.'_id_rsa > /dev/null 2>&1 ; cd '.$this->siteFolder.'; git pull origin '.$this->branch);
                }
            }

            if (! empty($this->sha)) {
                $output[] = $this->remoteTaskService->run("cd $this->release; git reset --hard $this->sha");
            }

            return $output;
        }
    }

    /**
     * @description Setups the folders for web service.
     *
     * @zerotime-deployment
     *
     * @order 400
     */
    public function setupFolders()
    {
        if ($this->zerotimeDeployment) {
            $currentFolder = $this->siteFolder.'/current';

            // Remove the docked with codepier index
            $this->remoteTaskService->run('ls -l '.$currentFolder.' | grep -v "\->" && rm -rf '.$currentFolder.' || echo true');

            return $this->remoteTaskService->run('ln -sfn '.$this->release.' '.$currentFolder);
        }
    }

    /**
     * @description Cleans up the old deploys.
     *
     * @zerotime-deployment
     *
     * @order 600
     */
    public function cleanup()
    {
        if ($this->zerotimeDeployment && $this->site->keep_releases > 0) {
            $this->remoteTaskService->ssh($this->server, 'root');

            return $this->remoteTaskService->run('cd '.$this->siteFolder.'; find . -maxdepth 1 -name "2*" | sort -r | tail -n +'.($this->site->keep_releases + 1).' | xargs rm -Rf');
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

        return $this->remoteTaskService->run('cd '.$this->release.' && '.$script);
    }
}
