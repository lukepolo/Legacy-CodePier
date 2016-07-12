<?php

namespace App\Services\Server\Providers;

use App\Models\Server;
use App\Models\ServerProvider;
use App\Models\ServerProviderFeatures;
use App\Models\ServerProviderOption;
use App\Models\ServerProviderRegion;
use App\Models\User;
use App\Services\Server\ServerService;
use DigitalOcean;
use DigitalOceanV2\Api\Key;
use DigitalOceanV2\Entity\Droplet;
use phpseclib\Crypt\RSA;

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
     * @param $sshKey
     * @param array $options
     * @return static
     * @throws \Exception
     */
    public function create(User $user, $name, $sshKey, array $options = [])
    {
        $sshPublicKey = new RSA();
        $sshPublicKey->loadKey($sshKey['publickey']);

        $ipv6 = false;
        $backups = false;
        $privateNetworking = false;
        
        $serverOption = ServerProviderOption::findOrFail($options['server_option']);
        $serverRegion = ServerProviderRegion::findOrFail($options['server_region']);

        foreach($options['features'] as $featureID) {
            $feature = lcfirst(ServerProviderFeatures::findOrFail($featureID)->feature);
            $$feature = true;
        }

        $this->setToken($user);

        DigitalOcean::key()->create($name, $sshKey['publickey']);

        /** @var Droplet $droplet */
        $droplet = DigitalOcean::droplet()->create(
            $name,
            $serverRegion->provider_name,
            strtolower($serverOption->getRamString()),
            ServerService::$serverOperatingSystem,
            $backups,
            $ipv6,
            $privateNetworking,
            $sshKeys = [
                $sshPublicKey->getPublicKeyFingerprint()
            ],
            $userData = null
        );

        return $this->saveServer($droplet, $user, $sshKey);
    }

    /**
     * @param Droplet $droplet
     * @param User $user
     * @param $sshKey
     * @return static
     * @throws \Exception
     */
    public function saveServer(Droplet $droplet, User $user, $sshKey)
    {
        $this->setToken($user);

        return Server::create([
            'user_id' => $user->id,
            'name' => $droplet->name,
            'server_id' => $droplet->id,
            'server_provider_id' => $this->getServerProviderID(),
            'status' => 'Provisioning',
            'public_ssh_key' => $sshKey['publickey'],
            'private_ssh_key' => $sshKey['privatekey']
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