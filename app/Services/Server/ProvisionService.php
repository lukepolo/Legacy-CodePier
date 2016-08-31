<?php

namespace App\Services\Server;

use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;
use App\Contracts\Server\ProvisionServiceContract;
use App\Events\Server\ServerProvisionStatusChanged;
use App\Exceptions\FailedCommand;
use App\Models\Server;

/**
 * Class ProvisionService
 * @package App\Services
 */
class ProvisionService implements ProvisionServiceContract
{
    protected $server;
    protected $totalActions;
    protected $doneActions = 0;
    protected $remoteTaskService;

    protected $provisionSystems = [
        'ubuntu 16.04' => 'Ubuntu\V_16_04'
    ];

    /**
     * ProvisionService constructor.
     * @param RemoteTaskService $remoteTaskService
     */
    public function __construct(RemoteTaskService $remoteTaskService)
    {
        $this->remoteTaskService = $remoteTaskService;
    }

    /**
     * Provisions a server based on its operating system
     * @param Server $server
     * @return bool
     */
    public function provision(Server $server)
    {
        $this->server = $server;

        try {
            foreach ($server->provisionSteps->filter(function($provisionStep) {
                return $provisionStep->completed == false;
            }) as $provisionStep) {
                $this->updateProgress($provisionStep->step);
                $systemService = $this->createSystemService($provisionStep->service);

                $systemService->{$provisionStep->function}();

                $provisionStep->failed = false;
                $provisionStep->completed = true;
                $provisionStep->log = $systemService->getOutput();
                $provisionStep->save();
            }
        } catch (FailedCommand $e) {
            $provisionStep->failed = true;
            $provisionStep->log = $systemService->getErrors();
            $provisionStep->save();
            return false;
        }

        return true;
    }

    /**
     * @param $status
     */
    private function updateProgress($status)
    {
        $totalDone = $this->server->provisionSteps->filter(function ($provisionStep) {
            return $provisionStep->completed;
        })->count();

        event(new ServerProvisionStatusChanged(
                $this->server,
                $status,
                floor(($totalDone / $this->server->provisionSteps->count()) * 100)
            )
        );
    }

    /**
     * @param $service
     * @return mixed
     */
    private function createSystemService($service)
    {
        $server = $this->server;
        // TODO - server needs to send in the correct system

        $service = 'App\Services\Server\Systems\\' . $this->provisionSystems['ubuntu 16.04'] . '\\' . $service;

        return new $service($this->remoteTaskService, $this->server);
    }
}