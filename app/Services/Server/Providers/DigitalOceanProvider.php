<?php

namespace App\Services\Server\Providers;

use Exception;
use GuzzleHttp\Client;
use phpseclib\Crypt\RSA;
use App\Models\User\User;
use App\Models\Server\Server;
use DigitalOceanV2\DigitalOceanV2;
use DigitalOceanV2\Entity\Droplet;
use App\Services\Server\ServerService;
use App\Models\User\UserServerProvider;
use DigitalOceanV2\Adapter\BuzzAdapter;
use GuzzleHttp\Exception\ClientException;
use App\Models\Server\Provider\ServerProviderOption;
use App\Models\Server\Provider\ServerProviderRegion;

/**
 * Class DigitalOcean.
 */
class DigitalOceanProvider implements ServerProviderContract
{
    const OAUTH_TOKEN_URL = 'https://cloud.digitalocean.com/v1/oauth/token';

    /** @var DigitalOceanV2 */
    private $client;

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

        $this->setToken($this->getTokenFromUser(\Auth::user()));

        foreach ($this->client->size()->getAll() as $size) {
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

        $this->setToken($this->getTokenFromUser(\Auth::user()));

        foreach ($this->client->region()->getAll() as $region) {
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
     *
     * @throws \Exception
     *
     * @return Server
     */
    public function create(Server $server)
    {
        $sshPublicKey = new RSA();
        $sshPublicKey->loadKey($server->public_ssh_key);

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

        $this->client->key()->create($server->name, $server->public_ssh_key);

        /** @var Droplet $droplet */
        $droplet = $this->client->droplet()->create(
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

        return $this->saveServer($server, $droplet->id);
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
        $this->setToken($this->getTokenFromServer($server));

        return $this->client->droplet()->getById($server->given_server_id)->status;
    }

    /**
     * Gets the server IP.
     *
     * @param \App\Models\Server\Server $server
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
     * @param \App\Models\Server\Server $server
     *
     * @return mixed
     */
    public function getPublicIP(Server $server)
    {
        $this->setToken($this->getTokenFromServer($server));

        $droplet = $this->client->droplet()->getById($server->given_server_id);

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
        $this->client = new DigitalOceanV2(new BuzzAdapter($token));
    }

    public function getUser(User $user)
    {
        $this->setToken($this->getTokenFromUser($user));

        return $this->client->account()->getUserInformation();
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
        $client = new Client();

        try {
            $response = $client->post(self::OAUTH_TOKEN_URL.'?grant_type=refresh_token&refresh_token='.$userServerProvider->refresh_token);
        } catch (ClientException $e) {
            throw new Exception(json_decode($e->getResponse()->getBody(), true));
        }

        if ($response->getStatusCode() == 200) {
            $tokenData = json_decode($response->getBody(), true);

            $userServerProvider->token = $tokenData['access_token'];
            $userServerProvider->refresh_token = $tokenData['refresh_token'];
            $userServerProvider->expires_in = $tokenData['expires_in'];

            $userServerProvider->save();

            return $userServerProvider->token;
        }

        throw new \Exception('Invalid refresh token');
    }

    public function readyForProvisioningStatus()
    {
        return 'active';
    }
}
