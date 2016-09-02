<?php

namespace App\Services\Systems\Ubuntu\V_16_04\Languages\PHP\Frameworks;

use App\Services\Systems\ServiceConstructorTrait;

class Laravel
{
    use ServiceConstructorTrait;

    public static $suggestedDefaults = [
        'Beanstalk',
        'Supervisor',
        'MariaDB',
        'Memcached',
        'Redis',
        'DiskMonitoringScript',
        'NodeJs',
        'Swap',
        'Git',
        'CertBot',
        'Nginx',
        'PHP7',
        'PhpFpm',
        'Composer',
        'BlackFire',
        'Envoy'
    ];

    public function installEnvoy()
    {
        $this->connectToServer('codepier');

        $this->remoteTaskService->run('composer global require "laravel/envoy=~1.0"');
    }

    public function copyExampleEnvironment()
    {
    }
}
