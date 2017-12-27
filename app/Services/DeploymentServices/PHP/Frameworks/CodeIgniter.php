<?php

namespace App\Services\DeploymentServices\PHP\Frameworks;

trait CodeIgniter
{
    /**
     * @description Creates a symbolic link for the cache and logs folders
     *
     * @zerotime-deployment
     *
     * @order 150
     */
    public function codeIgniterCreateSymbolicFolders()
    {
        $output = [];

        if ($this->zerotimeDeployment) {
            $output[] = $this->remoteTaskService->run('cp -r '.$this->release.'/application/logs '.$this->siteFolder);
            $output[] = $this->remoteTaskService->run('rm '.$this->release.'/application/logs -rf');
            $output[] = $this->remoteTaskService->run('ln -sfn '.$this->siteFolder.'/application/logs '.$this->release);

            $output[] = $this->remoteTaskService->run('cp -r '.$this->release.'/application/logs '.$this->siteFolder);
            $output[] = $this->remoteTaskService->run('rm '.$this->release.'/application/logs -rf');
            $output[] = $this->remoteTaskService->run('ln -sfn '.$this->siteFolder.'/application/logs '.$this->release);
        }

        return $output;
    }
}
