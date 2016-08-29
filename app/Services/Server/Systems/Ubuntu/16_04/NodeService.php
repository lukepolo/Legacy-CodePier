<?php

namespace App\Services\Server\ProvisionSystems;

class NodeService
{
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