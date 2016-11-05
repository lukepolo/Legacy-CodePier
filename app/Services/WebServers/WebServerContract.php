<?php

namespace App\Services\Systems\WebServers;

use App\Models\Server\Server;
use App\Models\Site\Site;

interface WebServerContract
{
    /**
     * @param Server $server
     * @param Site $site
     */
    public function updateWebServerConfig(Server $server, Site $site);
}
