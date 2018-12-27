<?php

namespace App\Services\Systems\Ubuntu\V_18_04\Languages\Swift\Frameworks;

use App\Models\Site\Site;
use App\Services\Systems\ServiceConstructorTrait;

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

    public function getNginxConfig(Site $site)
    {
        return '
            location / {
                try_files $uri @proxy;
            }
        
            location @proxy {
                proxy_pass http://127.0.0.1:'.$site->port.';
            }
        ';
    }
}
