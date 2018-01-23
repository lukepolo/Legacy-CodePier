<?php

namespace App\Services\Systems\Ubuntu\V_16_04\Languages\PHP\Frameworks;

use App\Models\Site\Site;
use App\Services\Systems\ServiceConstructorTrait;
use App\Services\Systems\Ubuntu\V_16_04\WebService;

class Laravel
{
    use ServiceConstructorTrait;

    public static $files = [
        '.env',
    ];

    public $suggestedFeatures = [
        'Languages\PHP\Frameworks\Laravel' => [
            'Envoy',
        ],
    ];

    public static $cronJobs = [
        'Laravel Scheduler' => '* * * * * php {site_path}/artisan schedule:run',
    ];

    /**
     * @description Laravel Envoy provides a clean, minimal syntax for defining common tasks you run on your remote servers.
     */
    public function installEnvoy()
    {
        $this->connectToServer('codepier');

        $this->remoteTaskService->run('composer global require "laravel/envoy=~1.0"');
    }

    public function getNginxConfig(Site $site, $phpVersion)
    {
        return '
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
        internal;
    }
';
    }
}
