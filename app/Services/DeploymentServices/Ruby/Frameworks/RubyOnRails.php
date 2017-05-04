<?php

namespace App\Services\DeploymentServices\Ruby\Frameworks;

trait RubyOnRails
{
    /**
     * @description Runs the migrations
     *
     * @order 210
     */
    public function rubyOnRailsRunMigrations()
    {
        return $this->remoteTaskService->run('cd '.$this->release.'; rake db:migrate');
    }

    /**
     * @description Runs the migrations
     *
     * @order 250
     */
    public function rubyOnRailsRunPrecompile()
    {
        return $this->remoteTaskService->run('cd '.$this->release.'; rake assets:precompile');
    }
}
