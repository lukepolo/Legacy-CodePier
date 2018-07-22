<?php

namespace App\Services\Systems\Ubuntu\V_16_04\Languages\Swift\Frameworks;

use App\Models\Site\Site;
use App\Services\Systems\ServiceConstructorTrait;
use App\Services\Systems\Ubuntu\V_16_04\WebService;

class Vapor
{
    use ServiceConstructorTrait;

    public static $files = [

    ];

    public $suggestedFeatures = [
        'Languages\Swift\Frameworks\Vapor' => [
            'Vapor',
        ],
    ];

    public static $cronJobs = [

    ];

    /**
     *  @name Vapor Tools
     *
     * @description Vapor tools allow you to run vapor applications
     */
    public function installVapor()
    {
        $this->connectToServer();
        $this->remoteTaskService->run('wget -q https://repo.vapor.codes/apt/keyring.gpg -O- | apt-key add -');
        $this->remoteTaskService->run('echo "deb https://repo.vapor.codes/apt $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/vapor.list');
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get update');
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install vapor -y');
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
