<?php

namespace App\Services\DeploymentServices\Ruby;

use App\Services\Systems\SystemService;
use App\Services\DeploymentServices\DeployTrait;
use App\Services\DeploymentServices\Ruby\Frameworks\RubyOnRails;

class Ruby
{
    use RubyOnRails;
    use DeployTrait;

    /**
     * @description Install the vendors packages.
     *
     * @order 200
     */
    public function installRubyDependencies()
    {
        $this->remoteTaskService->ssh($this->server, 'codepier');

        return [$this->remoteTaskService->run('cd '.$this->release.'; source /usr/local/rvm/scripts/rvm ;rvm use 2.4.0 ; bundle install --path .bundle')];
    }

    /**
     * @description Restart the web services
     *
     * @order 600
     */
    public function restartWebServices()
    {
        $this->remoteTaskService->ssh($this->server, 'root');

        return [$this->remoteTaskService->run('/opt/codepier/./'.SystemService::WEB_SERVICE_GROUP)];
    }
}
