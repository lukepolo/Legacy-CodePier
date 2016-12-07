<?php

namespace App\Services\Server\Providers;

use App\Models\Server\Server;
use App\Models\Server\Provider\ServerProvider;

trait ServerProviderTrait
{
    protected $providerName;

    /**
     * ServerProviderTrait constructor.
     * @param $providerName
     */
    public function __construct($providerName)
    {
        $this->providerName = $providerName;
    }

    /**
     * Saves the server information.
     *
     * @param \App\Models\Server\Server $server
     * @param $serverId
     * @param $sshKey
     * @return \App\Models\Server\Server
     */
    public function saveServer(Server $server, $serverId, $sshKey)
    {
        $this->setToken($this->getTokenFromServer($server));

        $server->fill([
            'given_server_id' => $serverId,
            'public_ssh_key' => $sshKey['publickey'],
            'private_ssh_key' => $sshKey['privatekey'],
        ])->save();

        return $server;
    }

    /**
     * Gets the server provider ID.
     *
     * @return mixed
     */
    private function getServerProviderID()
    {
        return \Cache::rememberForever('server.provider.'.$this->providerName.'.id', function () {
            return ServerProvider::where('provider_name', $this->providerName)->firstOrFail()->id;
        });
    }
}
