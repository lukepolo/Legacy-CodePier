<?php

namespace App\Services\Server\Providers;

use App\Models\Server\Provider\ServerProviderOption;
use App\Models\Server\Provider\ServerProviderRegion;
use App\Models\Server\Server;
use App\Models\User\UserServerProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class LinodeProvider implements ServerProviderContract
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
        $this->setToken($this->getTokenFromUser(\Auth::user()));
        $this->setAction('avail.linodeplans');

        $options = [];

        foreach ($this->makeRequest('get') as $plan) {
            $options[] = ServerProviderOption::firstOrCreate([
                'server_provider_id' => $this->getServerProviderID(),
                'memory' => $plan->RAM,
                'cpus' => $plan->CORES,
                'space' => $plan->DISK,
                'priceHourly' => $plan->HOURLY,
                'priceMonthly' => $plan->PRICE,
                'plan_id' => $plan->PLANID
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
        $this->setToken($this->getTokenFromUser(\Auth::user()));
        $this->setAction('avail.datacenters');

        $regions = [];

        foreach ($this->makeRequest('get') as $region) {
            $regions[] = ServerProviderRegion::firstOrCreate([
                'server_provider_id' => $this->getServerProviderID(),
                'name' => $region->LOCATION,
                'provider_name' => $region->ABBR,
                'region_id' => $region->DATACENTERID
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
        $this->setToken($this->getTokenFromServer($server));
        $this->setAction('avail.datacenters');

        $data = [
            'DatacenterID' => ServerProviderRegion::findOrFail($server->options['server_region'])->region_id,
            'PlanID' => ServerProviderOption::findOrFail($server->options['server_option'])->plan_id
        ];

        return $this->saveServer($server, $this->makeRequest('post', $data)->DATA->LinodeID);

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
        $this->setAction('avail.datacenters');

        $this->url = $this->url.'&LinodeID='.$server->given_server_id;

        $serverInfo = $this->makeRequest('get');

        return $serverInfo->DATA->STATUS;
    }

    /**
     * Gets the server IP.
     *
     * @param \App\Models\Server\Server $server
     */
    public function savePublicIP(Server $server)
    {
        $serverInfo = $this->getServerInfo($server);

        return $serverInfo->DATA->STATUS;
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
        $serverInfo = $this->getServerInfo($server);

        return $serverInfo->DATA->STATUS;
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
        $this->url = $this->url.'?api_key='.$token;
    }

    public function setAction($action)
    {
        $this->url = $this->url.'&api_action='.$action;
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
        // not needed for linode
    }

    /**
     * Gets the status that means its ready for provisioning.
     *
     * @return mixed
     */
    public function readyForProvisioningStatus()
    {
        return 1;
    }

    private function makeRequest($method)
    {
        $client = new Client();

        /** @var Response $response */
        $response = $client->$method($this->url);

        return json_decode($response->getBody())->DATA;
    }
}
