<?php

namespace App\Services\Server\Providers;

use App\Exceptions\LinodeInvalidAccount;
use GuzzleHttp\Client;
use App\Models\User\User;
use App\Models\Server\Server;
use GuzzleHttp\Psr7\Response;
use App\Models\User\UserServerProvider;
use App\Models\Server\Provider\ServerProviderOption;
use App\Models\Server\Provider\ServerProviderRegion;

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
            $option = ServerProviderOption::withTrashed()->firstOrNew([
                'external_id' => $plan->PLANID,
                'server_provider_id' => $this->getServerProviderID(),
            ]);

            $option->fill([
                'memory' => $plan->RAM,
                'cpus' => $plan->CORES,
                'space' => $plan->DISK,
                'priceHourly' => $plan->HOURLY,
                'priceMonthly' => $plan->PRICE,
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
        $this->setAction('avail.datacenters');

        $regions = [];

        foreach ($this->makeRequest('get') as $region) {
            $tempRegion = ServerProviderRegion::firstOrNew([
                'server_provider_id' => $this->getServerProviderID(),
                'external_id' => $region->DATACENTERID,
            ]);

            $tempRegion->fill([
                'name' => $region->LOCATION,
                'provider_name' => $region->ABBR,
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
        $this->setAction('linode.create');

        $serverProviderOption = ServerProviderOption::findOrFail($server->options['server_option']);

        $data = [
            'DatacenterID' => ServerProviderRegion::findOrFail($server->options['server_region'])->external_id,
            'PlanID' => $serverProviderOption->external_id,
        ];

        $serverInfo = $this->makeRequest('post', $data);

        $server = $this->saveServer($server, $serverInfo->LinodeID);

        $this->url = 'https://api.linode.com';
        $this->setToken($token);
        $this->setAction('linode.disk.createfromdistribution');

        $diskInfo = $this->makeRequest('post', [
            'label' => 'Ubuntu 16.04',
            'DistributionID'=> 146, // ubuntu 16.04
            'LinodeID' => $serverInfo->LinodeID,
            'rootPass' => $server->sudo_password,
            'rootSSHKey' => $server->public_ssh_key,
            'size' => $serverProviderOption->space * 1024,
        ]);

        $this->url = 'https://api.linode.com';
        $this->setToken($token);
        $this->setAction('linode.config.create');

        $this->makeRequest('post', [
            'LinodeID' => $serverInfo->LinodeID,
            // https://www.linode.com/kernels
            'KernelID' => 138, // Latest 64 bit (4.9.15-x86_64-linode81)
            'Label' => 'My Ubuntu 16.04 Profile',
            'DiskList' => $diskInfo->DiskID.',,,,,,,,',
        ]);

        $this->url = 'https://api.linode.com';
        $this->setToken($token);
        $this->setAction('linode.boot');

        $this->makeRequest('post', [
            'LinodeID' => $serverInfo->LinodeID,
        ]);

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
        $this->setToken($this->getTokenFromServer($server));
        $this->setAction('linode.list');

        $this->url = $this->url.'&LinodeID='.$server->given_server_id;

        $serverInfo = $this->makeRequest('get')[0];

        return $serverInfo->STATUS;
    }

    /**
     * Gets the server IP.
     *
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
        $this->setToken($this->getTokenFromServer($server));
        $this->setAction('linode.ip.list');

        $this->url = $this->url.'&LinodeID='.$server->given_server_id;

        $serverInfo = $this->makeRequest('get')[0];

        return $serverInfo->IPADDRESS;
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

    /**
     * @param $action
     */
    public function setAction($action)
    {
        $this->url = $this->url.'&api_action='.$action;
    }

    /**
     * @param UserServerProvider $userServerProvider
     * @return mixed
     * @throws LinodeInvalidAccount
     */
    public function getUser(UserServerProvider $userServerProvider)
    {
        $this->setToken($userServerProvider->token);
        $this->setAction('account.info');

        $userAccount = $this->makeRequest('get');

        if (! isset($userAccount->ACTIVE_SINCE)) {
            throw new LinodeInvalidAccount('Invalid Account');
        }

        return $userAccount;
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

    /**
     * @param $method
     * @param null $data
     * @return mixed
     * @throws \Exception
     */
    private function makeRequest($method, $data = null)
    {
        $client = new Client();

        /** @var Response $response */
        $response = $client->$method($this->url, [
            'form_params' => $data,
        ]);

        $data = json_decode($response->getBody());

        if (isset($data->DATA)) {
            return $data->DATA;
        }

        throw new \Exception($data);
    }
}
