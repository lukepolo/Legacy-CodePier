<?php

namespace App\Services\Systems;

use App\Models\Server\Server;
use App\Exceptions\FailedCommand;
use App\Contracts\Systems\SystemServiceContract;
use App\Events\Server\ServerProvisionStatusChanged;
use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;

class SystemService implements SystemServiceContract
{
    protected $server;
    protected $remoteTaskService;

    const PROVISION_SYSTEMS = [
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
    const DEPLOYMENT_SERVICE_GROUP = 'deployment_services';

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
     * @return bool
     * @throws \Exception
     */
    public function provision(Server $server)
    {
        $this->server = $server->load('provisionSteps');

        try {
            foreach ($server->provisionSteps->filter(function ($provisionStep) {
                return $provisionStep->completed == false;
            }) as $provisionStep) {
                $this->updateProgress($provisionStep->step);

                $systemService = $this->createSystemService($provisionStep->service, $server);

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
        } catch (\Exception $e) {
            if (config('app.debug')) {
                throw $e;
            }

            $provisionStep->failed = true;
            $provisionStep->log = 'We had a system error please contact support.';
            $provisionStep->save();

            $this->updateProgress($provisionStep->step);

            return false;
        }

        return true;
    }

    /**
     * @param $step
     */
    private function updateProgress($step)
    {
        event(new ServerProvisionStatusChanged(
                $this->server,
                $step,
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
    public function createSystemService($service, Server $server)
    {
        $service = 'App\Services\Systems\\'.self::PROVISION_SYSTEMS[$server->system_class].'\\'.$service;

        return new $service($this->remoteTaskService, ! empty($server) ? $server : $this->server);
    }
}
