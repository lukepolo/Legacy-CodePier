<?php

namespace App\Services\DeploymentServices\PHP;

use App\Models\Server\Server;
use App\Models\Site\Site;
use App\Services\DeploymentServices\PHP\Frameworks\Laravel;
use App\Services\RemoteTaskService;
use Carbon\Carbon;

class PHP
{
    use Laravel;

    private $branch;
    private $release;
    private $site_folder;
    private $repository;
    private $remoteTaskService;
    private $repositoryProvider;

    public $suggestedProvisioning = [
        'OsService' => [
            'Swap' => [
                'enabled' => 1,
            ],
            'parameters' => [
                'size' => '1G',
                'swappiness' => 10,
                'vfsCachePressure' => 50,
            ]
        ],
        'WebService' => [
            'Nginx' => [
                'enabled' => 1,
            ],
            'CertBot' => [
                'enabled' => 1
            ]
        ],
        'NodeService' => [
            'Yarn' => [
                'enabled' => 1
            ],
            'NodeJs' => [
                'enabled' => 1
            ]
        ],
        'WorkerService' => [
            'Beanstalk' => [
                'enabled' => 1
            ],
            'Supervisor' => [
                'enabled' => 1
            ]
        ],
        'DatabaseService' => [
            'Redis' => [
                'enabled' => 1
            ],
            'MariaDB' => [
                'enabled' => 1
            ]
        ],
        'Languages\PHP\PHP' => [
            'PHP7' => [
                'enabled' => 1
            ],
            'PhpFpm' => [
                'enabled' => 1
            ],
            'Composer' => [
                'enabled' => 1
            ]
        ],
        'MonitoringService' => [
            'DiskMonitoringScript' => [
                'enabled' => 1,
            ]
        ],
        'RepositoryService' => [
            'Git' => [
                'enabled' => 1
            ]
        ]
    ];

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

        $output[] = $this->remoteTaskService->run('eval `ssh-agent -s` > /dev/null 2>&1; ssh-add ~/.ssh/id_rsa > /dev/null 2>&1 ; cd '.$this->site_folder.'; git clone '.$this->repositoryProvider->git_url.':'.$this->repository.' --branch='.$this->branch.(empty($sha) ? ' --depth=1 ' : ' ').$this->release);

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
}
