<?php

namespace App\Services\Server\Providers;

use App\Models\Server;
use App\Models\ServerProvider;
use App\Models\ServerProviderOption;
use App\Models\ServerProviderRegion;
use App\Models\User;
use DigitalOcean;
use DigitalOceanV2\Entity\Droplet;

/**
 * Class DigitalOcean
 *
 * @package App\Services\Server\ServerProviders
 */
class DigitalOceanProvider
{
    protected $providerName = 'digitalocean';

    public function getOptions()
    {
        $options = [];
        $this->setToken(\Auth::user());

        foreach (DigitalOcean::size()->getAll() as $size) {

            $options[] = ServerProviderOption::firstOrCreate([
                'server_provider_id' => $this->getServerProviderID(),
                'memory' => $size->memory,
                'cpus' => $size->vcpus,
                'space' => $size->disk,
                'priceHourly' => $size->priceHourly,
                'priceMonthly' => $size->priceMonthly
            ]);
        }

        return $options;
    }

    public function getRegions()
    {
        $regions = [];
        $this->setToken(\Auth::user());

        foreach (DigitalOcean::region()->getAll() as $region) {
            $regions[] = ServerProviderRegion::firstOrCreate([
                'server_provider_id' => $this->getServerProviderID(),
                'name' => $region->name,
                'provider_name' => $region->slug
            ]);
        }

        return $regions;
    }

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
            'server_provider_id' => $this->getServerProviderID(),
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

        foreach ($droplet->networks as $network) {
            if ($network->type == 'public') {
                return $network->ipAddress;
            }
        }
    }

    private function setToken(User $user)
    {
        if ($serverProvider = $user->userServerProviders->where('id', 1)->first()) {
            return config(['digitalocean.connections.main.token' => $serverProvider->token]);
        }

        throw new \Exception('No server provider found for this user');
    }

    private function getServerProviderID()
    {
        return \Cache::rememberForever('server.provider.'.$this->providerName.'.id', function() {
            return ServerProvider::where('provider_name', $this->providerName)->first()->id;
        });
    }
}