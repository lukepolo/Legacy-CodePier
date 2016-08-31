<?php

namespace App\Services\Server\Systems\Traits;

use App\Models\Server;
use App\Services\RemoteTaskService;

trait ServiceConstructorTrait
{
    protected $server;
    /**
     * ProvisionService constructor.
     * @param RemoteTaskService $remoteTaskService
     * @param Server $server
     */
    public function __construct(RemoteTaskService $remoteTaskService, Server $server)
    {
        $this->server = $server;

        $this->remoteTaskService = $remoteTaskService;
        $this->connectToServer();

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