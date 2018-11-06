<?php

namespace App\Services\Server\Providers;

use Vultr\VultrClient;
use App\Models\Server\Server;
use App\Models\User\UserServerProvider;
use Vultr\Adapter\GuzzleHttpAdapter as Guzzle;
use App\Models\Server\Provider\ServerProviderOption;
use App\Models\Server\Provider\ServerProviderRegion;

class VultrProvider implements ServerProviderContract
{
    use ServerProviderTrait;


    /**
     * @var VultrClient
     */
    private $client;

    /**
     * Gets the server options from the provider.
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getOptions()
    {
        $this->setToken($this->getTokenFromUser(\Auth::user()));

        foreach ($this->client->metaData()->getPlansList() as $plan) {
            $option = ServerProviderOption::withTrashed()->firstOrNew([
                'external_id' => $plan['VPSPLANID'],
                'server_provider_id' => $this->getServerProviderID(),
            ]);

            $option->fill([
                'memory' => $plan['ram'],
                'cpus' => $plan['vcpu_count'],
                'space' => $plan['disk'],
                'priceHourly' => 0, // C'mon Vultr, how hard is it to tack on your hourly pricing data?! As such, and given the fact that hourly pricing is not purely mathematical, we can't provide this :(
                'priceMonthly' => $plan['price_per_month'],
            ]);

            $option->save();

            $options[] = $option;
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
        $this->setToken($this->getTokenFromUser(\Auth::user()));

        foreach ($this->client->region()->getList() as $region) {
            $tempRegion = ServerProviderRegion::firstOrNew([
                'server_provider_id' => $this->getServerProviderID(),
                'external_id' => $region['DCID'],
            ]);

            $tempRegion->fill([
                'name' => $region['name'],
                'provider_name' => $region['regioncode'],
            ]);

            $tempRegion->save();

            $regions[] = $tempRegion;
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
     * @return Server $server
     */
    public function create(Server $server)
    {
        $token = $this->getTokenFromServer($server);
        $this->setToken($token);

        $serverProviderOption = ServerProviderOption::findOrFail($server->options['server_option']);

        sleep(2);

        $sshKeyId = $this->client->sshKey()->create($server->name, $server->public_ssh_key);

        sleep(2);

        $serverOptions = [
            'DCID' => ServerProviderRegion::findOrFail($server->options['server_region'])->external_id,
            'VPSPLANID' => $serverProviderOption->external_id,
            'OSID' => 215, // Ubuntu 16.04 x64
            'hostname' => $server->name,
            'label' => $server->name,
            'SSHKEYID' => $sshKeyId,
        ];

        foreach ($server->getServerProviderFeatures() as $featureModel) {
            $serverOptions[$featureModel->option] = true;
        }

        $vultrServer = $this->client->server()->create($serverOptions);

        $server = $this->saveServer($server, $vultrServer);

        return $server;
    }

    /**
     * Gets the status of a server.
     *
     * @param \App\Models\Server\Server $server
     *
     * @return mixed
     * @throws \Exception
     */
    public function getStatus(Server $server)
    {
        $token = $this->getTokenFromServer($server);
        $this->setToken($token);

        sleep(2);

        return $this->client->server()->getDetail($server->given_server_id)['server_state'];
    }

    /**
     * Gets the server IP.
     * @param \App\Models\Server\Server $server
     * @throws \Exception
     */
    public function savePublicIP(Server $server)
    {
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
     * @throws \Exception
     */
    public function getPublicIP(Server $server)
    {
        $token = $this->getTokenFromServer($server);
        $this->setToken($token);

        sleep(2);

        return $this->client->server()->getDetail($server->given_server_id)['main_ip'];
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
        $this->client = new VultrClient(new Guzzle($token));
    }

    /**
     * @param UserServerProvider $userServerProvider
     * @return array|mixed
     * @throws \Exception
     */
    public function getUser(UserServerProvider $userServerProvider)
    {
        $this->setToken($userServerProvider->token);

        return $this->client->metaData()->getAccountInfo();
    }

    /**
     * Gets the status that means its ready for provisioning.
     *
     * @return mixed
     */
    public function readyForProvisioningStatus()
    {
        return 'ok';
    }
}
