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
}
