<?php

namespace App\Services\Server;

use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;
use App\Contracts\Server\ProvisionServiceContract as ProvisionService;
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
    protected $provisionService;

    public $providers = [
        'digitalocean' => Providers\DigitalOceanProvider::class
    ];

    public static $serverOperatingSystem = 'ubuntu-16-04-x64';

    /**
     * SiteService constructor.
     *
     * @param \App\Services\RemoteTaskService | RemoteTaskService $remoteTaskService
     * @param \App\Services\Server\ProvisionService | ProvisionService $provisionService
     */
    public function __construct(RemoteTaskService $remoteTaskService, ProvisionService $provisionService)
    {
        $this->remoteTaskService = $remoteTaskService;
        $this->provisionService = $provisionService;
    }

    /**
     * Creates a new server
     *
     * @param ServerProvider $serverProvider
     * @param User $user
     * @param $name
     * @param array $options
     * @return mixed
     */
    public function create(ServerProvider $serverProvider, User $user, $name, array $options)
    {
        return $this->getProvider($serverProvider)->create($user, $name, $options);
    }

    /**
     * Gets the server options available (ram, cpus , disk space, etc.)
     *
     * @param ServerProvider $serverProvider
     * @return mixed
     */
    public function getServerOptions(ServerProvider $serverProvider)
    {
        return $this->getProvider($serverProvider)->getOptions();
    }

    /**
     * Gets the servers regions available
     *
     * @param ServerProvider $serverProvider
     * @return mixed
     */
    public function getServerRegions(ServerProvider $serverProvider)
    {
        return $this->getProvider($serverProvider)->getRegions();
    }

    /**
     * Gets the status of the serer
     *
     * @param Server $server
     * @return mixed
     */
    public function getStatus(Server $server)
    {
        $server->touch();

        try {
            return $this->getProvider($server->serverProvider)->getStatus($server);
        } catch(\Exception $e) {
            if($e->getMessage() == 'The resource you were accessing could not be found.') {
                $server->delete();
                return 'Server Has Been Deleted';
            }
        }
    }

    /**
     * Saves the server information into the database
     *
     * @param Server $server
     * @return mixed
     */
    public function saveInfo(Server $server)
    {
        return $this->getProvider($server->serverProvider)->savePublicIP($server);
    }

    /**
     * Installs a SSH key onto a server
     *
     * @param Server $server
     * @param $sshKey
     */
    public function installSshKey(Server $server, $sshKey)
    {
//        echo {{ $sshKey }} >> ~/.ssh/authorized_keys
        $this->remoteTaskService->run(
            $server->ip,
            'codepier',
            'install_ssh_key', [
                'sshKey' => $sshKey
            ]
        );
    }

    /**
     * Removes an SSH key from the server
     *
     * @param Server $server
     * @param $sshKey
     */
    public function removeSshKey(Server $server, $sshKey)
    {
//        sed -i '/{{ $sshKey }}/d' ~/.ssh/authorized_keys
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
        $this->provisionService->provision($server);
    }

    /**
     * Gets the provider passed in
     *
     * @param ServerProvider $serverProvider
     * @return mixed
     */
    private function getProvider(ServerProvider $serverProvider)
    {
        return new $this->providers[$serverProvider->provider_name]();
    }

}