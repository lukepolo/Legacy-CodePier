<?php

namespace App\Services\Server\Providers;

use App\Models\Server;
use App\Models\ServerProviderOption;
use App\Models\ServerProviderRegion;
use App\Services\Server\ServerService;
use DigitalOcean;
use DigitalOceanV2\Entity\Droplet;
use phpseclib\Crypt\RSA;

/**
 * Class DigitalOcean.
 */
class DigitalOceanProvider implements ServerProviderContract
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
        $options = [];

        $this->setToken(env('ADMIN_DIGITAL_OCEAN_API_KEY'));

        foreach (DigitalOcean::size()->getAll() as $size) {
            $options[] = ServerProviderOption::firstOrCreate([
                'server_provider_id' => $this->getServerProviderID(),
                'memory' => $size->memory,
                'cpus' => $size->vcpus,
                'space' => $size->disk,
                'priceHourly' => $size->priceHourly,
                'priceMonthly' => $size->priceMonthly,
            ]);
        }

        return $options;
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
        $regions = [];

        $this->setToken(env('ADMIN_DIGITAL_OCEAN_API_KEY'));

        foreach (DigitalOcean::region()->getAll() as $region) {
            $regions[] = ServerProviderRegion::firstOrCreate([
                'server_provider_id' => $this->getServerProviderID(),
                'name' => $region->name,
                'provider_name' => $region->slug,
            ]);
        }

        return $regions;
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
        $sshPublicKey = new RSA();
        $sshPublicKey->loadKey($sshKey['publickey']);

        $ipv6 = false;
        $backups = false;
        $privateNetworking = false;

        $serverOption = ServerProviderOption::findOrFail($server->options['server_option']);
        $serverRegion = ServerProviderRegion::findOrFail($server->options['server_region']);

        foreach ($server->getServerProviderFeatures() as $featureModel) {
            $feature = lcfirst($featureModel->option);
            $$feature = 1;
        }

        $this->setToken($this->getTokenFromServer($server));

        DigitalOcean::key()->create($server->name, $sshKey['publickey']);

        /** @var Droplet $droplet */
        $droplet = DigitalOcean::droplet()->create(
            $server->name,
            $serverRegion->provider_name,
            strtolower($serverOption->getRamString()),
            ServerService::$serverOperatingSystem,
            $backups,
            $ipv6,
            $privateNetworking,
            $sshKeys = [
                $sshPublicKey->getPublicKeyFingerprint(),
            ],
            $userData = null
        );

        return $this->saveServer($server, $droplet->id, $sshKey);
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
        $this->setToken($this->getTokenFromServer($server));

        return DigitalOcean::droplet()->getById($server->server_id)->status;
    }

    /**
     * Gets the server IP.
     *
     * @param Server $server
     */
    public function savePublicIP(Server $server)
    {
        $this->setToken($this->getTokenFromServer($server));

        $server->update([
            'ip' => $this->getPublicIP($server),
        ]);
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
        $this->setToken($this->getTokenFromServer($server));

        $droplet = DigitalOcean::droplet()->getById($server->given_server_id);

        foreach ($droplet->networks as $network) {
            if ($network->type == 'public') {
                return $network->ipAddress;
            }
        }
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
        config(['digitalocean.connections.main.token' => $token]);
    }

    /**
     * Gets the token from the server.
     *
     * @param Server $server
     *
     * @throws \Exception
     *
     * @return mixed
     */
    private function getTokenFromServer(Server $server)
    {
        if ($serverProvider = $server->user->userServerProviders->where(
            'server_provider_id',
            $server->server_provider_id
        )->first()
        ) {
            return $serverProvider->token;
        }

        throw new \Exception('No server provider found for this user');
    }

    public function refreshToken()
    {
        dd('need to refresh token');
    }
}
