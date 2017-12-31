<?php

namespace App\Services\Systems\Ubuntu\V_16_04\Languages\Node;

use App\Models\CronJob;
use App\Models\Site\Site;
use App\Services\RemoteTaskService;
use App\Services\Systems\SystemService;
use App\Services\Systems\ServiceConstructorTrait;
use App\Services\Systems\Ubuntu\V_16_04\WebService;

class Node
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
        'FirewallService' => [
            'Fail2ban',
        ],
        'WorkerService' => [
            // TODO - add https://github.com/Unitech/pm2
        ],
        'DatabaseService' => [
            'Mongo',
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

    public function getNginxConfig(Site $site)
    {
        $frameworkConfig = '';
        if (! empty($site->framework)) {
            $frameworkConfig = $this->getFrameworkService($site)->getNginxConfig($site, $this->server->getLanguages()['PHP']['version']);
        }

        return '
    location / {
        proxy_pass http://localhost:8080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection \'upgrade\';
        proxy_set_header Host $host;
        proxy_cache_bypass $http_upgrade;
    }
    
    '.$frameworkConfig.'
  
    location ~ \.php$ {
        return 404;
    }
';
    }

    private function getFrameworkService(Site $site)
    {
        return create_system_service('Languages\\'.$site->getFrameworkClass(), $this->server);
    }
}
