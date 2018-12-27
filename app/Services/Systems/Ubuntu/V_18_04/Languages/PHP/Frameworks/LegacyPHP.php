<?php

namespace App\Services\Systems\Ubuntu\V_18_04\Languages\PHP\Frameworks;

use App\Models\Site\Site;
use App\Services\Systems\ServiceConstructorTrait;
use App\Services\Systems\Ubuntu\V_18_04\WebService;

class LegacyPHP
{
    use ServiceConstructorTrait;

    public static $files = [
    ];

    public $suggestedFeatures = [
    ];

    public static $cronJobs = [
    ];

    public function getNginxConfig(Site $site, $phpVersion)
    {
        return '
        
    location / {
        include '.WebService::NGINX_SERVER_FILES.'/'.$site->domain.'/root-location/*;
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_index index.php;
        fastcgi_pass unix:/var/run/php/php'.$phpVersion.'-fpm.sock;
        include fastcgi_params;
        
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
';
    }
}
