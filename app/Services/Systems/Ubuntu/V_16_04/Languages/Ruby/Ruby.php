<?php

namespace App\Services\Systems\Ubuntu\V_16_04\Languages\Ruby;

use App\Models\Site\Site;
use App\Services\RemoteTaskService;
use App\Services\Systems\ServiceConstructorTrait;
use App\Services\Systems\Ubuntu\V_16_04\WebService;

class Ruby
{
    use ServiceConstructorTrait;

    /** @var RemoteTaskService $remoteTaskService */
    private $remoteTaskService;

    public static $files = [

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
            'Ruby',
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
    public function installRuby($version = '2.4')
    {
        $this->connectToServer();

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y ruby ruby-dev zlib1g-dev liblzma-dev rng-tools');

        $this->remoteTaskService->run('gpg --keyserver hkp://keys.gnupg.net --recv-keys 409B6B1796C275462A1703113804BB82D39DC0E3');
        $this->remoteTaskService->run('\curl -sSL https://get.rvm.io | bash -s stable');
        $this->remoteTaskService->run('usermod -a -G rvm codepier');

        $this->remoteTaskService->run('apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv-keys 561F9B9CAC40B2F7');
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y apt-transport-https ca-certificates');

        $this->remoteTaskService->run('sh -c \'echo deb https://oss-binaries.phusionpassenger.com/apt/passenger xenial main > /etc/apt/sources.list.d/passenger.list\'');
        $this->remoteTaskService->run('apt-get update');

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y nginx-extras passenger');

        $this->remoteTaskService->findTextAndAppend('/etc/nginx/nginx.conf', 'include /etc/nginx/sites-enabled/*;', '## Passenger');
        $this->remoteTaskService->findTextAndAppend('/etc/nginx/nginx.conf', '## Passenger', 'include /etc/nginx/passenger.conf;');

        switch ($version) {
            case '2.3':
                $this->remoteTaskService->run('source /usr/local/rvm/scripts/rvm; rvm install 2.3.0');
                $this->remoteTaskService->run('source /usr/local/rvm/scripts/rvm; rvm use 2.3.0 --default');
                break;
            default:
                $this->remoteTaskService->run('source /usr/local/rvm/scripts/rvm; rvm install 2.4.0');
                $this->remoteTaskService->run('source /usr/local/rvm/scripts/rvm; rvm use 2.4.0 --default');
                break;
        }

        $this->remoteTaskService->appendTextToFile('/home/codepier/.bashrc', 'source /usr/local/rvm/scripts/rvm');
    }

    public function getNginxConfig(Site $site)
    {
        $this->connectToServer();

        $frameworkConfig = '';
        if (! empty($site->framework)) {
            $frameworkConfig = $this->getFrameworkService($site)->getNginxConfig();
        }

        return '

    '.$frameworkConfig.'
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    passenger_enabled on;
    passenger_ruby /usr/local/rvm/gems/ruby-2.4.0/wrappers/ruby;

';
    }

    private function getFrameworkService(Site $site)
    {
        return create_system_service('Languages\\'.$site->getFrameworkClass(), $this->server);
    }
}
