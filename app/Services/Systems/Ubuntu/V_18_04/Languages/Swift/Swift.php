<?php

namespace App\Services\Systems\Ubuntu\V_18_04\Languages\Swift;

use App\Models\Site\Site;
use App\Services\RemoteTaskService;
use App\Services\Systems\ServiceConstructorTrait;

class Swift
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
        'FirewallService' => [
            'Fail2ban',
        ],
        'WorkerService' => [
            'Supervisor',
        ],
        'DatabaseService' => [
            'Redis',
            'SqlLite',
            'PostgreSQL',
        ],
        'Languages\Swift\Swift' => [
            'Swift',
        ],
        'MonitoringService' => [
            'SchemaBackupScript',
            'DiskMonitoringScript',
            'LoadMonitoringScript',
            'ServerMemoryMonitoringScript',
        ],
        'RepositoryService' => [
            'Git',
        ],
    ];

    /**
     * @options 4.0,4.1,4.2
     * @multiple false
     */
    public function installSwift($version = '4.1')
    {
        $this->connectToServer('root');

        $this->remoteTaskService->run('git clone https://github.com/kylef/swiftenv.git /opt/.swiftenv');
        $this->remoteTaskService->run('chown codepier:codepier /opt/.swiftenv');

        $this->remoteTaskService->appendTextToFile('/etc/bash.bashrc', 'export SWIFTENV_ROOT="/opt/.swiftenv"');
        $this->remoteTaskService->appendTextToFile('/etc/bash.bashrc', 'export PATH="$SWIFTENV_ROOT/bin:$PATH"');
        $this->remoteTaskService->appendTextToFile('/etc/bash.bashrc', 'eval "$(swiftenv init -)"');

        $this->connectToServer('codepier');

        $this->remoteTaskService->run("swiftenv install $version");
    }

    public function getNginxConfig(Site $site)
    {
        return $this->getFrameworkService($site)->getNginxConfig($site);
    }

    private function getFrameworkService(Site $site)
    {
        return create_system_service('Languages\\'.$site->getFrameworkClass(), $this->server);
    }
}
