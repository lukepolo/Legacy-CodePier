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

    public $providers = [
        'digitalocean' => Providers\DigitalOceanProvider::class
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
     * @param $provider
     * @param User $user
     * @param $name
     * @param array $options
     * @return mixed
     */
    public function create($provider, User $user, $name, array $options)
    {
        return $this->getProvider($provider)->create($user, $name, $options);
    }

    /**
     * @param Server $server
     * @return mixed
     */
    public function getStatus(Server $server)
    {
        return $this->getProvider($server->service)->getStatus($server);
    }

    /**
     * @param Server $server
     * @return mixed
     */
    public function saveInfo(Server $server)
    {
        return $this->getProvider($server->service)->savePublicIP($server);
    }

    /**
     * @param Server $server
     * @return bool
     */
    public function provision(Server $server)
    {
        if($this->remoteTaskService->run(
            $server->ip,
            'root',
            'provision', [
                'branch' => 'master',
                'path' => '/home/codepier/laravel'
            ],
            true
        )) {
            $server->status = 'Active';
            $server->save();

            return true;
        }

        return false;
    }

    /**
     * @param $provider
     * @return mixed
     */
    private function getProvider($provider)
    {
        return new $this->providers[$provider]();
    }

}