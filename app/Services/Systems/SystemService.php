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
    const NODE = 'NodeService';
    const SYSTEM = 'OsService';
    const WORKERS = 'WorkerService';
    const FIREWALL = 'FirewallService';
    const DATABASE = 'DatabaseService';
    const MONITORING = 'MonitoringService';
    const REPOSITORY = 'RepositoryService';

    const WEB_SERVICE_GROUP = 'web_services';
    const WORKER_SERVICE_GROUP = 'worker_services';
    const DATABASE_SERVICE_GROUP = 'database_services';
    const DEPLOYMENT_SERVICE_GROUP = 'deployment_services';

    const LANGUAGES = [
        'PHP' => 'Languages\PHP\PHP',
        'Ruby' => 'Languages\Ruby\Ruby',
    ];

    const FRAMEWORKS = [
        'Languages\PHP\Frameworks\Laravel',
    ];

    const WEB_SERVER = 'web';
    const WORKER_SERVER = 'worker';
    const DATABASE_SERVER = 'database';
    const LOAD_BALANCER = 'load_balancer';
    const FULL_STACK_SERVER = 'full_stack';

    const SERVER_TYPES = [
        'Full Stack' => self::FULL_STACK_SERVER,
        'Web' => self::WEB_SERVER,
        'Worker' => self::WORKER_SERVER,
        'Database' => self::DATABASE_SERVER,
        'Load Balancer' => self::LOAD_BALANCER,
    ];

    const LANGUAGES_GROUP = 'Languages';

    const SERVER_TYPE_FEATURE_GROUPS = [
        self::FULL_STACK_SERVER => [
            self::WEB,
            self::NODE,
            self::SYSTEM,
            self::WORKERS,
            self::FIREWALL,
            self::DATABASE,
            self::MONITORING,
            self::REPOSITORY,
            self::LANGUAGES_GROUP,
        ],
        self::WEB_SERVER => [
            self::WEB,
            self::NODE,
            self::SYSTEM,
            self::FIREWALL,
            self::MONITORING,
            self::REPOSITORY,
            self::LANGUAGES_GROUP,
        ],
        self::WORKER_SERVER => [
            self::NODE,
            self::SYSTEM,
            self::WORKERS,
            self::MONITORING,
            self::REPOSITORY,
            self::LANGUAGES_GROUP,
        ],
        self::DATABASE_SERVER => [
            self::SYSTEM,
            self::DATABASE,
            self::MONITORING,
            self::REPOSITORY,
        ],
        self::LOAD_BALANCER => [
            self::WEB,
            self::SYSTEM,
            self::FIREWALL,
            self::MONITORING,
        ],
    ];

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

                $provisionStep->update([
                    'failed' => false,
                    'completed' => true,
                    'log' => $systemService->getOutput(),
                ]);
            }
        } catch (FailedCommand $e) {
            $provisionStep->update([
                'failed' => true,
                'log' => $systemService->getErrors(),
            ]);

            $this->updateProgress($provisionStep->step);

            return false;
        } catch (\Exception $e) {
            if (config('app.debug')) {
                throw $e;
            }

            $provisionStep->update([
                'failed' => true,
                'log' => 'We had a system error please contact support.',
            ]);

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
