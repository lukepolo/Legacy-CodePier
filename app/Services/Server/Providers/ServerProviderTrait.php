<?php

namespace App\Services\Server\Providers;

use Carbon\Carbon;
use App\Models\User\User;
use App\Models\Server\Server;
use App\Models\Server\Provider\ServerProvider;

trait ServerProviderTrait
{
    protected $providerName;

    /**
     * ServerProviderTrait constructor.
     *
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
     *
     * @return \App\Models\Server\Server
     */
    public function saveServer(Server $server, $serverId)
    {
        $this->setToken($this->getTokenFromServer($server));

        $server->update([
            'given_server_id' => $serverId,
        ]);

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

    /**
     * Gets the token from the server.
     *
     * @param User $user
     *
     * @throws \Exception
     *
     * @return mixed
     */
    private function getTokenFromUser(User $user)
    {
        $providerName = $this->providerName;

        $server_provider_id = \Cache::rememberForever($providerName.'Id', function () use ($providerName) {
            return ServerProvider::where('provider_name', $providerName)->first()->id;
        });

        if ($serverProvider = $user->userServerProviders->where(
            'server_provider_id',
            $server_provider_id
        )->first()
        ) {
            if (! empty($serverProvider->expires_at) && Carbon::now()->gte($serverProvider->expires_at)) {
                return $this->refreshToken($serverProvider);
            }

            return $serverProvider->token;
        }

        throw new \Exception('No server provider found for this user');
    }

    /**
     * Gets the token from the server.
     *
     * @param \App\Models\Server\Server $server
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
            if (! empty($serverProvider->expires_at) && Carbon::now()->gte($serverProvider->expires_at)) {
                return $this->refreshToken($serverProvider);
            }

            return $serverProvider->token;
        }

        throw new \Exception('No server provider found for this user');
    }
}
