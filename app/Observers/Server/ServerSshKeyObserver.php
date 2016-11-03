<?php

namespace App\Observers\Server;

/**
 * Class ServerSshKeyObserver.
 */
class ServerSshKeyObserver
{
    private $serverService;

    /**
     * Create a new job instance.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     */
    public function __construct(ServerService $serverService)
    {
        $this->serverService = $serverService;
    }

    public function created()
    {

    }
}
