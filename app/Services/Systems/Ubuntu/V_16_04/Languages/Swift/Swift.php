<?php

namespace App\Services\Systems\Ubuntu\V_16_04\Languages\Swift;

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

        $this->remoteTaskService->appendTextToFile('/etc/profile', 'export SWIFTENV_ROOT="/opt/.swiftenv"');
        $this->remoteTaskService->appendTextToFile('/etc/profile', 'export PATH="$SWIFTENV_ROOT/bin:$PATH"');
        $this->remoteTaskService->appendTextToFile('/etc/profile', 'eval "$(swiftenv init -)"');

        $this->connectToServer('codepier');

        $this->remoteTaskService->run('echo $PATH');

        $this->remoteTaskService->run("swiftenv install $version");
    }

    public function getNginxConfig(Site $site)
    {
        return '';
        /*$phpVersion = $this->server->getLanguages()['PHP']['version'];
        $frameworkConfig = '

    location / {
        include '.WebService::NGINX_SERVER_FILES.'/'.$site->domain.'/root-location/*;
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php/php'.$phpVersion.'-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;

        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
    }
';
        if (! empty($site->framework)) {
            $frameworkConfig = $this->getFrameworkService($site)->getNginxConfig($site, $phpVersion);
        }

        return '
    index index.html index.htm index.php;

    '.$frameworkConfig.'

    location ~ \.php$ {
        return 404;
    }
';*/
    }

    private function getFrameworkService(Site $site)
    {
        return create_system_service('Languages\\'.$site->getFrameworkClass(), $this->server);
    }
}
