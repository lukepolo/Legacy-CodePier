<?php

namespace App\Services\Server\Providers;

use App\Models\Server;

class Vultr implements ServerProviderContract
{
    /**
     * Gets the server options from the provider.
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getOptions()
    {
    }

    /**
     * Gets the regions from the provider.
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getRegions()
    {
    }

    /**
     * Creates a new server.
     *
     * @param Server $server
     * @param $sshKey
     *
     * @throws \Exception
     *
     * @return static
     */
    public function create(Server $server, $sshKey)
    {
    }

    /**
     * Gets the status of a server.
     *
     * @param Server $server
     *
     * @return mixed
     */
    public function getStatus(Server $server)
    {
    }

    /**
     * Gets the server IP.
     *
     * @param Server $server
     */
    public function savePublicIP(Server $server)
    {
    }

    /**
     * Gets the public IP of the server.
     *
     * @param Server $server
     *
     * @return mixed
     */
    public function getPublicIP(Server $server)
    {
    }

    /**
     * Sets the token for the API.
     *
     * @param $token
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function setToken($token)
    {
    }

    /**
     * Refreshes the token.
     *
     * @return mixed
     */
    public function refreshToken()
    {
    }
}
