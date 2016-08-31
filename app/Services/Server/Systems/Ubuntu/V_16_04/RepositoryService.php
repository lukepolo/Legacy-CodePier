<?php

namespace App\Services\Server\Systems\Ubuntu\V_16_04;

use App\Services\Server\Systems\Traits\ServiceConstructorTrait;

class RepositoryService
{
    use ServiceConstructorTrait;

    public function installGit()
    {
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y git');

        $this->remoteTaskService->run('echo git config --global user.name "'.$this->server->user->name.'"');
        $this->remoteTaskService->run('echo git config --global user.email '.$this->server->user->email.'');
    }
}