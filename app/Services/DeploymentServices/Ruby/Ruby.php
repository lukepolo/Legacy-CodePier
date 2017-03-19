<?php

namespace App\Services\DeploymentServices\Ruby;

use Carbon\Carbon;
use App\Models\Site\Site;
use App\Models\Server\Server;
use App\Models\Site\SiteDeployment;
use App\Services\RemoteTaskService;
use App\Services\Systems\SystemService;
use App\Services\DeploymentServices\Ruby\Frameworks\RubyOnRails;

class Ruby
{
    use RubyOnRails;

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

        $this->repositoryProvider = $site->userRepositoryProvider->repositoryProvider;
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
            $this->remoteTaskService->run('ssh-keyscan -t rsa '.$this->repositoryProvider->url.' | tee -a ~/.ssh/known_hosts');

            $url = 'git://'.$this->repositoryProvider->url.'/'.$this->repository.'.git';

            if ($this->site->private) {
                $url = $this->repositoryProvider->git_url.':'.$this->repository;
            }

            if ($this->zerotimeDeployment) {
                $output[] = $this->remoteTaskService->run('eval `ssh-agent -s` > /dev/null 2>&1; ssh-add ~/.ssh/'.$this->site->id.'_id_rsa > /dev/null 2>&1 ; cd '.$this->siteFolder.'; git clone '.$url.' --branch='.$this->branch.(empty($this->sha) ? ' --depth=1' : '').' '.$this->release);
            } else {
                if (! $this->remoteTaskService->hasDirectory($this->siteFolder.'/.git')) {
                    $output[] = $this->remoteTaskService->run('eval `ssh-agent -s` > /dev/null 2>&1; ssh-add ~/.ssh/'.$this->site->id.'_id_rsa > /dev/null 2>&1 ; cd '.$this->siteFolder.'; git clone '.$url.' --branch='.$this->branch.' .');
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
            return [$this->remoteTaskService->run('ln -sfn '.$this->release.' '.$this->siteFolder.($this->zerotimeDeployment ? '/current' : null))];
        }
    }

    /**
     * @description Cleans up the old deploys.
     *
     * @zerotime-deployment
     *
     * @order 500
     */
    public function cleanup()
    {
        if ($this->zerotimeDeployment && $this->site->keep_releases > 0) {
            $this->remoteTaskService->ssh($this->server, 'root');

            return [$this->remoteTaskService->run('cd '.$this->siteFolder.'; find . -maxdepth 1 -name "2*" | sort -r | tail -n +'.($this->site->keep_releases + 1).' | xargs rm -Rf')];
        }
    }

//    /**
//     * @description Restart the deployment services
//     *
//     * @order 600
//     */
//    public function restartPhpFpm()
//    {
//        $this->remoteTaskService->ssh($this->server, 'root');
//
//        return [$this->remoteTaskService->run('/opt/codepier/./'.SystemService::DEPLOYMENT_SERVICE_GROUP)];
//    }

    /**
     * Runs a custom step on the server.
     * @param $script
     */
    public function customStep($script)
    {
        $this->remoteTaskService->run('cd '.$this->release.' && '.$script);
    }
}
