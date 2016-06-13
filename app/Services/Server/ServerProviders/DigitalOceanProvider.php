<?php

namespace App\Services\Server\ServerProviders;

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
     * @param array $options
     * @return static
     */
    public function create(User $user, array $options = [])
    {
        /** @var Droplet $droplet */
        $droplet = DigitalOcean::droplet()->create(
            \Request::get('name'),
            'nyc3',
            '512mb',
            'ubuntu-16-04-x64',
            $backups = true,
            $ipv6 = true,
            $privateNetworking = true,
            $sshKeys = [
                '06:73:25:f2:3f:cb:1a:04:fd:c2:f3:1e:b6:9f:e1:39'
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
        return Server::create([
            'user_id' => $user->id,
            'name' => $droplet->name,
            'server_id' => $droplet->id,
            'service' => 'digitalocean'
        ]);
    }

    /**
     * @param Server $server
     * @return mixed
     */
    public function getStatus(Server $server)
    {
        return DigitalOcean::droplet()->getById($server->server_id)->status;
    }

    /**
     * @param Server $server
     */
    public function savePublicIP(Server $server)
    {
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
        $droplet = DigitalOcean::droplet()->getById($server->server_id);

        foreach($droplet->networks as $network) {
            if($network->type == 'public') {
                return $network->ipAddress;
            }
        }
    }
}