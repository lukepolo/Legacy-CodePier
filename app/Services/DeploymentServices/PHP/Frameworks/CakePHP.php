<?php

namespace App\Services\DeploymentServices\PHP\Frameworks;

trait CakePHP
{
    /**
     * @description Creates a symbolic link for the env file
     *
     * @zero_downtime_deployment
     *
     * @order 250
     */
    public function cakePHPCreatePluginSymlinks()
    {
        $this->remoteTaskService->ssh($this->server, 'codepier');

        return $this->remoteTaskService->run('cd '.$this->release.'; php bin/cake plugin assets symlink');
    }

    /**
     * @description Creates a symbolic link for the env file
     *
     * @order 270
     */
    public function cakePHPRunMigrations()
    {
        $this->remoteTaskService->ssh($this->server, 'codepier');

        $output = [];

        $output[] = $this->remoteTaskService->run('cd '.$this->release.'; php bin/cake migrations migrate');
        $output[] = $this->remoteTaskService->run('cd '.$this->release.'; php bin/cake orm_cache clear');
        $output[] = $this->remoteTaskService->run('cd '.$this->release.'; php bin/cake orm_cache build');

        return $output;
    }
}
