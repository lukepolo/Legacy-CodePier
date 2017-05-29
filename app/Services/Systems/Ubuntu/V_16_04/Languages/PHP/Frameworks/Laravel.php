<?php

namespace App\Services\Systems\Ubuntu\V_16_04\Languages\PHP\Frameworks;

use App\Services\AbstractService;

class Laravel extends AbstractService
{

    public static $files = [
        '.env',
    ];

    public $suggestedFeatures = [
        'Languages\PHP\Frameworks\Laravel' => [
            'Envoy',
        ],
    ];

    public static $cronJobs = [
        'Laravel Scheduler' => '* * * * * php {site_path} artisan schedule:run >> /dev/null 2>&1',
    ];

    /**
     * @description Laravel Envoy provides a clean, minimal syntax for defining common tasks you run on your remote servers.
     */
    public function installEnvoy()
    {
        $this->connectToServer('codepier');

        $this->remoteTaskService->run('composer global require "laravel/envoy=~1.0"');
    }

    public function getNginxConfig()
    {
    }
}
