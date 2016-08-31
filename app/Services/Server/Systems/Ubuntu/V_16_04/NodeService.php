<?php

namespace App\Services\Server\Systems\Ubuntu\V_16_04;

use App\Services\Server\Systems\Traits\ServiceConstructorTrait;

class NodeService
{
    use ServiceConstructorTrait;

    public function installNodeJs()
    {
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y nodejs npm');
    }

    public function installGulp()
    {
        $this->remoteTaskService->run('npm install -g gulp');
    }

    public function installBower()
    {
        $this->remoteTaskService->run('npm install -g bower');
    }
}