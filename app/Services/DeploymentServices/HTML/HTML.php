<?php

namespace App\Services\DeploymentServices\HTML;

use App\Services\DeploymentServices\DeployTrait;

class HTML
{
    use DeployTrait;

    /**
     * @description Install the node vendors packages using npm install.
     *
     * @order 300
     *
     * @not_default
     */
    public function installNodeDependencies()
    {
        $output = [];

        $nvm = '';

        if ($this->remoteTaskService->hasFile($this->release.'/.nvmrc')) {
            $version = $this->remoteTaskService->getFileContents($this->release.'/.nvmrc');
            $nvm = "nvm install $version && nvm use &&";
        }

        $output[] = $this->remoteTaskService->run("cd $this->release; $nvm npm install;");

        return $output;
    }

    /**
     * @description Install the node vendors packages.
     *
     * @order 301
     *
     * @not_default
     */
    public function installNodeDependenciesWithYarn()
    {
        $output = [];

        $nvm = '';

        if ($this->remoteTaskService->hasFile($this->release.'/.nvmrc')) {
            $version = $this->remoteTaskService->getFileContents($this->release.'/.nvmrc');
            $nvm = "nvm install $version && nvm use &&";
        }

        $output[] = $this->remoteTaskService->run("cd $this->release; $nvm yarn install --no-progress;");

        return $output;
    }
}
