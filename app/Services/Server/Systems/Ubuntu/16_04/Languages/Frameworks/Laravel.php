<?php

namespace App\Services\Server\ProvisionSystems;

class Laravel
{
    public function installLaravelInstaller()
    {
        $this->remoteTaskService->run('sudo su codepier; composer global require "laravel/installer=~1.1"');
    }

    public function installEnvoy()
    {
        $this->remoteTaskService->run('sudo su codepier; composer global require "laravel/envoy=~1.0"');
    }
}