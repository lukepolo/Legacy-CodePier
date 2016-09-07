<?php

namespace App\Services\Systems;

use App\Models\Server;
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

    public function connectToServer($user = 'root')
    {
        $this->remoteTaskService->ssh($this->server);
    }

    public function getErrors()
    {
        return $this->remoteTaskService->getErrors();
    }

    public function getOutput()
    {
        return $this->remoteTaskService->getOutput();
    }

    public function addToServiceRestartGroup($group, $command)
    {
        return $this->remoteTaskService->appendTextToFile('/opt/codepier/'.$group, $command);
    }

    public function restartWebServices()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('/opt/codepier/./'.SystemService::WEB_SERVICE_GROUP);
    }

    public function restartDatabase()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('/opt/codepier/./'.SystemService::WEB_SERVICE_GROUP);
    }

    public function restartWorkers()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('/opt/codepier/./'.SystemService::WORKER_SERVICE_GROUP);
    }
}
