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

    public function installEnvoy()
    {
        $this->connectToServer('codepier');

        $this->remoteTaskService->run('composer global require "laravel/envoy=~1.0"');
    }

    private function getNginxConfig()
    {
    }
}
