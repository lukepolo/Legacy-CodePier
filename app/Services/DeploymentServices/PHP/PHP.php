<?php

namespace App\Services\DeploymentServices\PHP;

use App\Services\DeploymentServices\DeployTrait;
use App\Services\Systems\SystemService;
use App\Services\DeploymentServices\PHP\Frameworks\Laravel;

class PHP
{
    use Laravel;
    use DeployTrait;

    /**
     * @description Install the vendors packages.
     *
     * @order 200
     */
    public function installPhpDependencies()
    {
        return [$this->remoteTaskService->run('cd '.$this->release.'; composer install --no-progress --no-interaction')];
    }

    /**
     * @description Install the node vendors packages.
     *
     * @order 300
     */
    public function installNodeDependencies()
    {
        $output = [];

        $output[] = $this->remoteTaskService->run('([ -d '.$this->siteFolder.'/node_modules ]) || (cd '.$this->release.'; yarn install --no-progress --production; mv '.$this->release.'/node_modules '.$this->siteFolder.')');
        $output[] = $this->remoteTaskService->run('ln -s '.$this->siteFolder.'/node_modules '.$this->release);

        return $output;
    }

    /**
     * @description Restart the deployment services
     *
     * @order 600
     */
    public function restartPhpFpm()
    {
        $this->remoteTaskService->ssh($this->server, 'root');

        return [$this->remoteTaskService->run('/opt/codepier/./'.SystemService::DEPLOYMENT_SERVICE_GROUP)];
    }

}
