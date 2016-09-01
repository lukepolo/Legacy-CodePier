<?php

namespace App\Services\Systems\Ubuntu\V_16_04;

use App\Services\Systems\Traits\ServiceConstructorTrait;

class RepositoryService
{
    use ServiceConstructorTrait;

    public function installGit()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y git');

        $this->remoteTaskService->run('echo git config --global user.name "'.$this->server->user->name.'"');
        $this->remoteTaskService->run('echo git config --global user.email '.$this->server->user->email.'');
    }
}