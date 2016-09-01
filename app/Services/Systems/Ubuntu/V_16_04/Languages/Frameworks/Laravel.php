<?php

namespace App\Services\Server\Systems\Ubuntu\V_16_04\Languages\Frameworks;

use App\Services\Systems\Traits\ServiceConstructorTrait;

class Laravel
{
    use ServiceConstructorTrait;

    public function installEnvoy()
    {
        $this->connectToServer('codepier');

        $this->remoteTaskService->run('composer global require "laravel/envoy=~1.0"');
    }
}
