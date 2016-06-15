<?php

namespace App\Services\Server\Providers;

use App\Models\Server;
use App\Models\User;
use DigitalOceanV2\Entity\Droplet;
use DigitalOcean;

/**
 * Class DigitalOcean
 *
 * @package App\Services\Server\ServerProviders
 */
class DigitalOceanProvider {

    /**
     * @param User $user
     * @param $name
     * @param array $options
     * @return static
     * @throws \Exception
     */
    public function create(User $user, $name, array $options = [])
    {
        $this->setToken($user);

        /** @var Droplet $droplet */

        // TODO - server should be configurable
        $droplet = DigitalOcean::droplet()->create(
            $name,
            'nyc3',
            '512mb',
            'ubuntu-16-04-x64',
            $backups = true,
            $ipv6 = true,
            $privateNetworking = true,
            $sshKeys = [
                env('SSH_KEY')
            ],
            $userData = ''
        );

        return $this->saveServer($droplet, $user);
    }

    /**
     * @param Droplet $droplet
     * @return static
     */
    public function saveServer(Droplet $droplet, User $user)
    {
        $this->setToken($user);

        return Server::create([
            'user_id' => $user->id,
            'name' => $droplet->name,
            'server_id' => $droplet->id,
            'service' => 'digitalocean',
            'status' => 'Provisioning',
        ]);
    }

    /**
     * @param Server $server
     * @return mixed
     */
    public function getStatus(Server $server)
    {
        $this->setToken($server->user);

        return DigitalOcean::droplet()->getById($server->server_id)->status;
    }

    /**
     * @param Server $server
     */
    public function savePublicIP(Server $server)
    {
        $this->setToken($server->user);

        $server->update([
            'ip' => $this->getPublicIP($server)
        ]);
    }

    /**
     * @param Server $server
     * @return mixed
     */
    public function getPublicIP(Server $server)
    {
        $this->setToken($server->user);

        $droplet = DigitalOcean::droplet()->getById($server->server_id);

        foreach($droplet->networks as $network) {
            if($network->type == 'public') {
                return $network->ipAddress;
            }
        }
    }

    private function setToken(User $user)
    {
        if($serverProvider = $user->serverProviders->where('service', 'digitalocean')->first()) {
            return config(['digitalocean.connections.main.token' => $serverProvider->token]);
        }

        throw new \Exception('No server provider found for this user');
    }
}