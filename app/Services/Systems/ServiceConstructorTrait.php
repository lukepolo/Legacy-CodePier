<?php

namespace App\Services\Systems;

use App\Models\Server\Server;
use App\Services\RemoteTaskService;

trait ServiceConstructorTrait
{
    protected $server;

    /**
     * @param RemoteTaskService $remoteTaskService
     * @param Server            $server
     */
    public function __construct(RemoteTaskService $remoteTaskService, Server $server)
    {
        $this->server = $server;

        $this->remoteTaskService = $remoteTaskService;
    }

    private function connectToServer($user = 'root')
    {
        $this->remoteTaskService->ssh($this->server);
    }

    private function getErrors()
    {
        return $this->remoteTaskService->getErrors();
    }

    private function getOutput()
    {
        return $this->remoteTaskService->getOutput();
    }

    private function addToServiceRestartGroup($group, $command)
    {
        $serviceGroupFile = '/opt/codepier/'.$group;
        $this->remoteTaskService->appendTextToFile($serviceGroupFile, $command);

        $this->remoteTaskService->run('chmod 775 '.$serviceGroupFile);
    }

    private function restartWebServices()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('/opt/codepier/./'.SystemService::WEB_SERVICE_GROUP);
    }

    private function restartDatabase()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('/opt/codepier/./'.SystemService::WEB_SERVICE_GROUP);
    }

    private function restartWorkers()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('/opt/codepier/./'.SystemService::WORKER_SERVICE_GROUP);
    }
}
