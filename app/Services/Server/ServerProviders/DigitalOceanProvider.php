<?php

namespace App\Services\Server\ServerProviders;

use App\Models\UserServer;
use DigitalOceanV2\Entity\Droplet;
use DigitalOcean;

/**
 * Class DigitalOcean
 *
 * @package App\Services\Server\ServerProviders
 */
class DigitalOceanProvider {

    public function create()
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

        return $this->saveUserServer($droplet);
    }

    public function saveUserServer(Droplet $droplet)
    {
        return UserServer::create([
            'user_id' => \Auth::user()->id,
            'name' => $droplet->name,
            'server_id' => $droplet->id,
            'service' => 'digitalocean'
        ]);
    }

    public function getStatus(UserServer $userServer)
    {
        return DigitalOcean::droplet()->getById($userServer->server_id)->status;
    }

    public function savePublicIP(UserServer $userServer)
    {
        $userServer->update([
            'ip' => $this->getPublicIP($userServer)
        ]);
    }

    public function getPublicIP(UserServer $userServer)
    {
        $droplet = DigitalOcean::droplet()->getById($userServer->server_id);

        foreach($droplet->networks as $network) {
            if($network->type == 'public') {
                return $network->ipAddress;
            }
        }
    }
}