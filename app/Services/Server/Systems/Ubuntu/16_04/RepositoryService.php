<?php

namespace App\Services\Server\ProvisionSystems;

class RepositoryService
{
    public function installGit(Server $server)
    {
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y git');

        $this->remoteTaskService->run('echo git config --global user.name "'.$server->user->name.'"');
        $this->remoteTaskService->run('echo git config --global user.email '.$server->user->email.'');
    }
}