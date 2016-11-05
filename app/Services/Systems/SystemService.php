<?php

namespace App\Services\Systems;

use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;
use App\Contracts\Systems\SystemServiceContract;
use App\Events\Server\ServerProvisionStatusChanged;
use App\Exceptions\FailedCommand;
use App\Models\Server\Server;

class SystemService implements SystemServiceContract
{
    protected $server;
    protected $remoteTaskService;

    protected $provisionSystems = [
        'ubuntu 16.04' => 'Ubuntu\V_16_04',
    ];

    const WEB = 'WebService';
    const SYSTEM = 'OsService';
    const WORKERS = 'WorkerService';
    const FIREWALL = 'FirewallService';
    const DATABASE = 'DatabaseService';
    const MONITORING = 'MonitoringService';
    const REPOSITORY = 'RepositoryService';

    const PHP = 'Languages\PHP';
    const LARAVEL = 'Languages\Frameworks\Laravel';

    const WEB_SERVICE_GROUP = 'web_services';
    const WORKER_SERVICE_GROUP = 'worker_services';
    const DATABASE_SERVICE_GROUP = 'database_services';

    const SSL_FILES = '/etc/opt/ssl';
    const LETS_ENCRYPT = 'Let\'s Encrypt';

    /**
     * @param RemoteTaskService $remoteTaskService
     */
    public function __construct(RemoteTaskService $remoteTaskService)
    {
        $this->remoteTaskService = $remoteTaskService;
    }

    /**
     * Provisions a server based on its operating system.
     *
     * @param \App\Models\Server\Server $server
     *
     * @return bool
     */
    public function provision(Server $server)
    {
        $this->server = $server;

        try {
            foreach ($server->provisionSteps->filter(function ($provisionStep) {
                return $provisionStep->completed == false;
            }) as $provisionStep) {
                $this->updateProgress($provisionStep->step);

                $systemService = $this->createSystemService($provisionStep->service);

                call_user_func_array([$systemService, $provisionStep->function], $provisionStep->parameters);

                $provisionStep->failed = false;
                $provisionStep->completed = true;
                $provisionStep->log = $systemService->getOutput();
                $provisionStep->save();
            }
        } catch (FailedCommand $e) {
            $provisionStep->failed = true;
            $provisionStep->log = $systemService->getErrors();
            $provisionStep->save();

            $this->updateProgress($provisionStep->step);

            return false;
        }

        return true;
    }

    /**
     * @param $status
     */
    private function updateProgress($status)
    {
        event(new ServerProvisionStatusChanged(
                $this->server,
                $status,
                $this->server->provisioningProgress()
            )
        );
    }

    /**
     * @param $service
     * @param \App\Models\Server\Server|null $server
     *
     * @return mixed
     */
    public function createSystemService($service, Server $server = null)
    {
        // TODO - server needs to send in the correct system

        $service = 'App\Services\Systems\\'.$this->provisionSystems['ubuntu 16.04'].'\\'.$service;

        return new $service($this->remoteTaskService, ! empty($server) ? $server : $this->server);
    }
}
