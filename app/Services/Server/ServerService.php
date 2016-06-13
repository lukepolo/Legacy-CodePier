<?php

namespace App\Services\Server;

use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;
use App\Contracts\Server\ServerServiceContract;
use App\Models\Server;
use App\Models\User;

/**
 * Class ServerService
 * @package App\Services
 */
class ServerService implements ServerServiceContract
{
    protected $remoteTaskService;

    public $services = [
        'digitalocean' => ServerProviders\DigitalOceanProvider::class
    ];

    /**
     * SiteService constructor.
     * @param \App\Services\RemoteTaskService | RemoteTaskService $remoteTaskService
     */
    public function __construct(RemoteTaskService $remoteTaskService)
    {
        $this->remoteTaskService = $remoteTaskService;
    }

    /**
     * @param $service
     * @param User $user
     * @param array $options
     * @return mixed
     */
    public function create($service, User $user, array $options)
    {
        return $this->getService($service)->create($user, $options);
    }

    /**
     * @param Server $server
     * @return mixed
     */
    public function getStatus(Server $server)
    {
        return $this->getService($server->service)->getStatus($server);
    }

    /**
     * @param Server $server
     * @return mixed
     */
    public function saveInfo(Server $server)
    {
        return $this->getService($server->service)->savePublicIP($server);
    }

    /**
     * @param Server $server
     * @return bool
     */
    public function provision(Server $server)
    {
        return $this->remoteTaskService->run(
            $server->ip,
            'root',
            'provision', [
                'branch' => 'master',
                'path' => '/home/codepier/laravel'
            ],
            true
        );
    }

    /**
     * @param $service
     * @return mixed
     */
    private function getService($service)
    {
        return new $this->services[$service]();
    }

}