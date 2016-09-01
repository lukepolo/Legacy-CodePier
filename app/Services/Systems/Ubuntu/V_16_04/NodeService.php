<?php

namespace App\Services\Systems\Ubuntu\V_16_04;

use App\Services\Systems\Traits\ServiceConstructorTrait;

class NodeService
{
    use ServiceConstructorTrait;

    public function installNodeJs()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y nodejs npm');
    }

    public function installGulp()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('npm install -g gulp');
    }

    public function installBower()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('npm install -g bower');
    }
}
