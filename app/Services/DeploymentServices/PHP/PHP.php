<?php

namespace App\Services\DeploymentServices\PHP;

use App\Services\Systems\SystemService;
use App\Services\DeploymentServices\DeployTrait;
use App\Services\DeploymentServices\PHP\Frameworks\CakePHP;
use App\Services\DeploymentServices\PHP\Frameworks\Laravel;
use App\Services\DeploymentServices\PHP\Frameworks\Symfony;
use App\Services\DeploymentServices\PHP\Frameworks\CodeIgniter;

class PHP
{
    use DeployTrait;
    use CakePHP, CodeIgniter, Laravel, Symfony;

    /**
     * @description Install the vendors packages
     *
     * @order 200
     */
    public function installPhpDependencies()
    {
        $this->remoteTaskService->ssh($this->server, 'codepier');

        return $this->remoteTaskService->run('cd ' . $this->release . '; composer install --no-dev --no-progress --no-interaction --optimize-autoloader');
    }

    /**
     * @description Install the node vendors packages.
     *
     * @order 300
     *
     * @not_default true
     */
    public function installNodeDependencies()
    {
        $this->remoteTaskService->ssh($this->server, 'codepier');

        $output = [];

        $nvm = '';

        if ($this->remoteTaskService->hasFile($this->release.'/.nvmrc')) {
            $version = $this->remoteTaskService->getFileContents($this->release.'/.nvmrc');
            $nvm = "nvm install $version && nvm use &&";
        }

        $output[] = $this->remoteTaskService->run("cd $this->release; $nvm npm install --no-progress;");

        return $output;
    }

    /**
     * @description Install the node vendors packages.
     *
     * @order 301
     *
     * @not_default true
     */
    public function installNodeDependenciesWithYarn()
    {
        $this->remoteTaskService->ssh($this->server, 'codepier');

        $output = [];

        $nvm = '';

        if ($this->remoteTaskService->hasFile($this->release.'/.nvmrc')) {
            $version = $this->remoteTaskService->getFileContents($this->release.'/.nvmrc');
            $nvm = "nvm install $version && nvm use &&";
        }

        $output[] = $this->remoteTaskService->run("cd $this->release; $nvm yarn install --no-progress;");

        return $output;
    }

    /**
     * @description Restart the deployment services
     *
     * @order 500
     */
    public function restartPhpFpm()
    {
        $this->remoteTaskService->ssh($this->server, 'root');

        return $this->remoteTaskService->run('/opt/codepier/./' . SystemService::DEPLOYMENT_SERVICE_GROUP);
    }
}
