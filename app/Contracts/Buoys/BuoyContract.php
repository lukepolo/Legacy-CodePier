<?php

namespace App\Contracts\Buoys;

use App\Models\Server\Server;

interface BuoyContract
{
    /**
     * @param Server $server
     * @param array ...$parameters
     *
     * @buoy-param $memory = 2g
     *
     */
    public function install(Server $server, ... $parameters);

    /**
     * When a bouy is set to a domain we must gather the web config
     * @param Server $server
     * return string
     */
    public function nginxConfig(Server $server);

    /**
     * When a bouy is set to a domain we must gather the web config
     * @param Server $server
     * return string
     */
    public function apacheConfig(Server $server);
}
