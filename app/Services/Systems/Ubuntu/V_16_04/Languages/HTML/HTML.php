<?php

namespace App\Services\Systems\Ubuntu\V_16_04\Languages\HTML;

use App\Models\Site\Site;
use App\Services\Systems\ServiceConstructorTrait;
use App\Services\Systems\Ubuntu\V_16_04\WebService;

class HTML
{
    use ServiceConstructorTrait;

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
        'DatabaseService' => [
            'Redis',
            'MySQL',
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
        $config = '
    location / {
        include '.WebService::NGINX_SERVER_FILES.'/'.$site->domain.'/root-location/*;
        try_files $uri $uri/ $uri.html =404;
    }
';
        if (! empty($site->framework)) {
            $config = $this->getFrameworkService($site)->getNginxConfig($site);
        }

        return '
    index index.html;

    '.$config.'
';
    }

    private function getFrameworkService(Site $site)
    {
        return create_system_service('Languages\\'.$site->getFrameworkClass(), $this->server);
    }
}
