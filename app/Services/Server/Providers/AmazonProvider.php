<?php

namespace App\Services\Server\Providers;

use GuzzleHttp\Client;
use App\Models\Server\Server;
use App\Models\User\UserServerProvider;
use App\Models\Server\Provider\ServerProviderOption;
use App\Models\Server\Provider\ServerProviderRegion;

class AmazonProvider implements ServerProviderContract
{
    use ServerProviderTrait;

    private $url = 'https://api.linode.com';

    /**
     * Gets the server options from the provider.
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getOptions()
    {
        ini_set('memory_limit', '-1');

        $awsPricing = \Cache::rememberForever('aws-pricing102', function () {
            $url = 'https://pricing.us-east-1.amazonaws.com/offers/v1.0/aws/AmazonEC2/current/index.json';

            $client = new Client();

            $response = $client->get($url);

            return json_decode($response->getBody()->getContents());
        });

        $instances = collect();

        foreach ($awsPricing->products as $product) {
            if (str_contains($product->productFamily, [
                'General Purpose',
            ])) {
                $attributes = $product->attributes;
                $instances->push([
                    'name' => $attributes->instanceType,
                    'cpus' => $attributes->vcpu,
                    'memory' => $attributes->memory,
                ]);
            }
        }

        dd($instances);
        die;

        dd('gettting options');
        $this->setToken($this->getTokenFromUser(\Auth::user()));

        $options = [];

        foreach ($this->makeRequest('get') as $plan) {
            $options[] = ServerProviderOption::firstOrCreate([
                'server_provider_id' => $this->getServerProviderID(),
                'memory' => $plan->RAM,
                'cpus' => $plan->CORES,
                'space' => $plan->DISK,
                'priceHourly' => $plan->HOURLY,
                'priceMonthly' => $plan->PRICE,
                'plan_id' => $plan->PLANID,
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
        dd('regions');
        $this->setToken($this->getTokenFromUser(\Auth::user()));

        $regions = [];

        foreach ($this->makeRequest('get') as $region) {
            $regions[] = ServerProviderRegion::firstOrCreate([
                'server_provider_id' => null,
                'name' => null,
                'provider_name' => null,
                'region_id' => null,
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
        dd('crate server');
        $token = $this->getTokenFromServer($server);
        $this->setToken($token);

        //        $serverProviderOption = ServerProviderOption::findOrFail($server->options['server_option']);
        //
        //        $data = [
        //            'DatacenterID' => ServerProviderRegion::findOrFail($server->options['server_region'])->region_id,
        //            'PlanID' => $serverProviderOption->plan_id,
        //        ];
        //
        //

        return $server;
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
        dd('get server status');
    }

    /**
     * Gets the server IP.
     *
     * @param \App\Models\Server\Server $server
     */
    public function savePublicIP(Server $server)
    {
        dd('get public ip');
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
        dd('get public ip');
        $this->setToken($this->getTokenFromServer($server));
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
        dd('set token');
        $this->url = $this->url.'?api_key='.$token;
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
        // not needed for amazon
    }

    /**
     * Gets the status that means its ready for provisioning.
     *
     * @return mixed
     */
    public function readyForProvisioningStatus()
    {
        dd('find out the status it should be');

        return 1;
    }
}
