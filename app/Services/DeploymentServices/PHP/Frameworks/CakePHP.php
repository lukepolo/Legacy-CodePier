<?php

namespace App\Services\DeploymentServices\PHP\Frameworks;

trait CakePHP
{
    /**
     * @description Creates a symbolic link for the env file
     *
     * @zerotime-deployment
     *
     * @order 210
     */
    public function cakePHPCreatePluginSymlinks()
    {
        return $this->remoteTaskService->run('cd '.$this->release.'; php bin/cake plugin assets symlink');
    }

    /**
     * @description Creates a symbolic link for the env file
     *
     * @order 210
     */
    public function cakePHPRunMigrations()
    {
        $output = [];

        $output[] = $this->remoteTaskService->run('cd '.$this->release.'; php bin/cake migrations migrate');
        $output[] = $this->remoteTaskService->run('cd '.$this->release.'; php bin/cake orm_cache clear');
        $output[] = $this->remoteTaskService->run('cd '.$this->release.'; php bin/cake orm_cache build');

        return $output;
    }
}
