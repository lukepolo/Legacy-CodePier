<?php

namespace app\Services\Server\Providers;

use App\Models\Server;
use App\Models\User;
use DigitalOceanV2\Api\Droplet;

/**
 * Interface ServerProviderContract
 * @package app\Services\Server\Providers
 */
interface ServerProviderContract
{
    /**
     * Gets the server options from the provider
     * @return array
     * @throws \Exception
     */
    public function getOptions();

    /**
     * Gets the regions from the provider
     * @return array
     * @throws \Exception
     */
    public function getRegions();

    /**
     * Creates a new server
     * @param User $user
     * @param $name
     * @param $sshKey
     * @param array $options
     * @return static
     * @throws \Exception
     */
    public function create(User $user, $name, $sshKey, array $options = []);

    /**
     * Saves the server information
     * @param Droplet $droplet
     * @param User $user
     * @param $sshKey
     * @return static
     * @throws \Exception
     */
    public function saveServer(Droplet $droplet, User $user, $sshKey);

    /**
     * Gets the status of a server
     * @param Server $server
     * @return mixed
     */
    public function getStatus(Server $server);

    /**
     * Gets the server IP
     * @param Server $server
     */
    public function savePublicIP(Server $server);

    /**
     * Gets the public IP of the server
     * @param Server $server
     * @return mixed
     */
    public function getPublicIP(Server $server);
}