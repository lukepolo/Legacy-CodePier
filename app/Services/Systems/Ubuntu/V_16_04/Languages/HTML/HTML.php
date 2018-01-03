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

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";
    
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    access_log off;
    error_log  /var/log/nginx/'.$site->domain.' error;
  
    '.$config.'


    location ~ /\.ht {
        deny all;
    }
';
    }

    private function getFrameworkService(Site $site)
    {
        return create_system_service('Languages\\'.$site->getFrameworkClass(), $this->server);
    }
}
