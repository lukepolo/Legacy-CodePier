<?php

namespace Tests;

use App\Models\Server\Server;

trait InteractsWithServers
{
    /**
     * Retrieve an instance of the development server.
     *
     * @return Server
     */
    private function getServer()
    {
        return Server::where('name', 'Development Environment')->get()->reverse()->first();
    }

    /**
     * Connect to the development server.
     *
     * @return boolean
     */
    private function connect()
    {
        $this->remoteTaskService = app()->make('App\Services\RemoteTaskService');
        return $this->remoteTaskService->ssh($this->getServer());
    }
}
