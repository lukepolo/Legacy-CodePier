<?php

namespace App\Services\Systems\Ubuntu\V_18_04\Languages\PHP\Frameworks;

use App\Models\Site\Site;
use App\Services\Systems\ServiceConstructorTrait;
use App\Services\Systems\Ubuntu\V_18_04\WebService;

class CakePHP
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
        try_files $uri $uri/ /index.php?$args;
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
