<?php

namespace App\Services\Systems\Ubuntu\V_16_04\Languages\Ruby;

use App\Models\CronJob;
use App\Models\Site\Site;
use App\Services\RemoteTaskService;
use App\Services\Systems\SystemService;
use App\Services\Systems\ServiceConstructorTrait;

class Ruby
{
    use ServiceConstructorTrait;

    /** @var RemoteTaskService $remoteTaskService */
    private $remoteTaskService;

    public static $files = [
        'Ruby' => [
        ],
    ];

    public $suggestedFeatures = [
        'OsService' => [
            'Swap',
        ],
        'WebService' => [
            'Nginx',
            'CertBot',
        ],
        'NodeService' => [
            'Yarn',
            'NodeJs',
        ],
        'WorkerService' => [
            'Beanstalk',
            'Supervisor',
        ],
        'DatabaseService' => [
            'Redis',
            'PostgreSQL',
        ],
        'Languages\Ruby\Ruby' => [
            'Ruby'
        ],
        'MonitoringService' => [
            'DiskMonitoringScript',
            'LoadMonitoringScript',
            'ServerMemoryMonitoringScript',
        ],
        'RepositoryService' => [
            'Git',
        ],
    ];

    /**
     * @description Ruby
     *
     * @options 2.3, 2.4
     * @multiple false
     * @param string $version
     */
    public function installRuby($version = '2.3')
    {
        $this->connectToServer();

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y ruby rng-tools');
        $this->remoteTaskService->run('gpg --keyserver hkp://keys.gnupg.net --recv-keys 409B6B1796C275462A1703113804BB82D39DC0E3');
        $this->remoteTaskService->run('\curl -sSL https://get.rvm.io | bash -s stable');
        $this->remoteTaskService->run('source /etc/profile.d/rvm.sh');

        switch ($version) {
            case '2.3':
                break;
            default:
                $this->remoteTaskService->run('rvm install 2.4.0');
                break;
        }
    }

    public function getNginxConfig(Site $site)
    {
        $frameworkConfig = '';
        if (! empty($site->framework)) {
            $frameworkConfig = $this->getFrameworkService($site)->getNginxConfig();
        }

        $this->setUpstream($site);

        return '

    '.$frameworkConfig.'
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

	location / {
		try_files $uri @app;
	}

	location @app {
		proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
		proxy_set_header X-Forwarded-Proto $scheme;
		proxy_set_header Host $http_host;
		proxy_redirect off;
		proxy_pass '.($this->site->activeSsl() ? 'https' : 'http').'://'.$site->domain.';
	}
    
    location ~ ^/(assets)/  {
		gzip_static on; # to serve pre-gzipped version
		expires max;
		add_header Cache-Control public;
	}
';
    }

    public function setUpstream(Site $site)
    {
        return '
    upstream '.$site->domain.' {
        server unix:'.$site->path.'/shared/sockets/unicorn.sock fail_timeout=0;
    }
';
    }

    private function getFrameworkService(Site $site)
    {
        return create_system_service('Languages\\'.$site->getFrameworkClass(), $this->server);
    }
}
