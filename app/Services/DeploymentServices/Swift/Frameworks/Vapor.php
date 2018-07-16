<?php

namespace App\Services\DeploymentServices\Swift\Frameworks;

trait Vapor
{
    /**
     * @description Some Command
     *
     * @zero_downtime_deployment
     *
     * @order 210
     */
    public function vaporSomeCommand()
    {
        $this->remoteTaskService->ssh($this->server, 'codepier');

        $output = [];
    }
}
