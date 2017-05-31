<?php

namespace App\Services\Systems\Ubuntu\V_16_04;

use App\Contracts\RemoteTaskServiceContract;
use App\Services\AbstractService;
use Illuminate\Database\Eloquent\Collection;

class RepositoryService extends AbstractService
{
    /**
     *  @description GIt is a is a version control system
     */
    public function installGit()
    {
        $this->remoteTaskService->connect($this->server);
        $this->remoteTaskService->run([
        'DEBIAN_FRONTEND=noninteractive apt-get install -y git',
        'git config --global user.name "'.$this->server->user->name.'"',
        'git config --global user.email "'.$this->server->user->email.'"',
        ]);
    }
}
