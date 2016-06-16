<?php

namespace App\Services\Server;

use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;
use App\Contracts\Server\ServerServiceContract;
use App\Models\Server;
use App\Models\ServerProvider;
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
     * Creates a new server
     *
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
     * @param $provider
     * @return mixed
     */
    public function getServerOptions($provider)
    {
        return $this->getProvider($provider)->getOptions();
    }

    /**
     * @param $provider
     * @return mixed
     */
    public function getServerRegions($provider)
    {
        return $this->getProvider($provider)->getRegions();
    }

    /**
     * Gets the status of the serer
     *
     * @param Server $server
     * @return mixed
     */
    public function getStatus(Server $server)
    {
        return $this->getProvider($server->service)->getStatus($server);
    }

    /**
     * Saves the server information into the database
     *
     * @param Server $server
     * @return mixed
     */
    public function saveInfo(Server $server)
    {
        return $this->getProvider($server->service)->savePublicIP($server);
    }

    /**
     * Installs a SSH key onto a server
     *
     * @param Server $server
     * @param $sshKey
     */
    public function installSshKey(Server $server, $sshKey)
    {
        $this->remoteTaskService->run(
            $server->ip,
            'codepier',
            'install_ssh_key', [
                'sshKey' => $sshKey
            ]
        );
    }

    /**
     * @param Server $server
     * @param $sshKey
     */
    public function removeSshKey(Server $server, $sshKey)
    {
        $this->remoteTaskService->run(
            $server->ip,
            'codepier',
            'remove_ssh_key', [
                'sshKey' => $sshKey
            ]
        );
    }

    /**
     * Provisions a server
     *
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
     * Gest the provider passed in
     * @param ServerProvider 
     * @return mixed
     */
    private function getProvider(ServerProvider $serverProvider)
    {
        return new $this->providers[$serverProvider->provider_name]();
    }

}