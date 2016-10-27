<?php

namespace App\Services\Systems\WebServers;

use App\Models\Server;
use App\Models\Site;

/**
 * Interface WebServerContract
 * @package App\Services\Systems\WebServers
 */
interface WebServerContract
{
    /**
     * @param Server $server
     * @param Site $site
     */
    public function updateWebServerConfig(Server $server, Site $site);

}