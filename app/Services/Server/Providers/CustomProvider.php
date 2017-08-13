<?php

namespace App\Services\Server\Providers;

use App\Models\Server\Server;
use App\Models\User\UserServerProvider;

class CustomProvider implements ServerProviderContract
{
    use ServerProviderTrait;

    /**
     * Gets the server options from the provider.
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getOptions()
    {
        return [];
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
        return [];
    }

    /**
     * Creates a new server.
     *
     * @param Server $server
     *
     * @throws \Exception
     *
     * @return static
     */
    public function create(Server $server)
    {
    }

    /**
     * Gets the status of a server.
     *
     * @param \App\Models\Server\Server $server
     *
     * @return mixed
     */
    public function getStatus(Server $server)
    {
        return 'active';
    }

    /**
     * Gets the server IP.
     *
     * @param \App\Models\Server\Server $server
     *
     * @return mixed
     */
    public function savePublicIP(Server $server)
    {
        return $server->ip;
    }

    /**
     * Gets the public IP of the server.
     *
     * @param \App\Models\Server\Server $server
     *
     * @return mixed
     */
    public function getPublicIP(Server $server)
    {
        return $server->ip;
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
     * @param UserServerProvider $userServerProvider
     * @return mixed
     * @throws \Exception
     */
    public function refreshToken(UserServerProvider $userServerProvider)
    {
    }

    public function readyForProvisioningStatus()
    {
        return 'active';
    }
}
