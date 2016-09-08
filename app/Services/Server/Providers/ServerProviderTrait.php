<?php

namespace App\Services\Server\Providers;

use App\Models\Server;
use App\Models\ServerProvider;

/**
 * Interface ServerProviderContract.
 */
trait ServerProviderTrait
{
    protected $providerName;

    /**
     * Saves the server information.
     *
     * @param Server $server
     * @param $serverProviderID
     * @param $sshKey
     * @return Server
     */
    public function saveServer(Server $server, $serverProviderID, $sshKey)
    {
        $this->setToken($this->getTokenFromServer($server));

        $server->fill([
            'server_id' => $serverProviderID,
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
