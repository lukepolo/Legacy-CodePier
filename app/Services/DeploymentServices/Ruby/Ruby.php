<?php

namespace App\Services\DeploymentServices\Ruby;

use App\Services\DeploymentServices\DeployTrait;
use App\Services\DeploymentServices\Ruby\Frameworks\RubyOnRails;

class Ruby
{
    /**
     * @description Install the vendors packages.
     *
     * @order 200
     */
    public function installRubyDependencies()
    {
        $this->remoteTaskService->ssh($this->server);
        return [$this->remoteTaskService->run('cd '.$this->release.'; bundle install --path ~/.gem/'.$this->site->domain)];
    }

    use RubyOnRails;
    use DeployTrait;
}
