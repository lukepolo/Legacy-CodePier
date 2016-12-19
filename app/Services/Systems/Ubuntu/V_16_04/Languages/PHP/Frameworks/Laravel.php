<?php

namespace App\Services\Systems\Ubuntu\V_16_04\Languages\PHP\Frameworks;

use App\Services\Systems\ServiceConstructorTrait;

class Laravel
{
    use ServiceConstructorTrait;

    public static $files = [
        '.env',
    ];

    public $suggestedFeatures = [];

    /**
     * @description Laravel Envoy provides a clean, minimal syntax for defining common tasks you run on your remote servers.
     */
    public function installEnvoy()
    {
        $this->connectToServer('codepier');

        $this->remoteTaskService->run('composer global require "laravel/envoy=~1.0"');
    }

    private function getNginxConfig()
    {
    }
}
