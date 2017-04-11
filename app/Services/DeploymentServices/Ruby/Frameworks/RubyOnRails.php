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
        return [$this->remoteTaskService->run('cd '.$this->release.'; source /usr/local/rvm/scripts/rvm ;rvm use 2.4.0 ; rake db:migrate')];
    }

    /**
     * @description Runs the migrations
     *
     * @order 250
     */
    public function rubyOnRailsRunPrecompile()
    {
        return [$this->remoteTaskService->run('cd '.$this->release.'; source /usr/local/rvm/scripts/rvm ; rvm use 2.4.0 ;rake assets:precompile')];
    }
}
