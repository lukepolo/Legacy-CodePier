<?php

namespace App\Services\Systems\WebServers;

use App\Models\Server;
use App\Models\Site;

/**
 * Interface WebServerContract.
 */
interface WebServerContract
{
    /**
     * @param Server $server
     * @param Site $site
     */
    public function updateWebServerConfig(Server $server, Site $site);
}
