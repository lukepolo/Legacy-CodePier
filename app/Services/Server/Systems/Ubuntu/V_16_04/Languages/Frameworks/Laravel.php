<?php

namespace App\Services\Server\Systems\Ubuntu\V_16_04\Languages\Frameworks;
use App\Services\Server\Systems\Traits\ServiceConstructorTrait;

class Laravel
{
    use ServiceConstructorTrait;

    public function installEnvoy()
    {
//        $this->remoteTaskService->run('sudo su codepier; composer global require "laravel/envoy=~1.0"');
    }
}