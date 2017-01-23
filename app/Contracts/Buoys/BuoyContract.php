<?php

namespace App\Contracts\Buoys;

interface BuoyContract
{
    /**
     * @param array $ports
     * @param array $parameters
     */
    public function install($ports, $parameters);

    /**
     * When a buoy is set to a domain we must gather the web config.
     * return string.
     */
    public function nginxConfig();

    /**
     * When a buoy is set to a domain we must gather the web config.
     * return string.
     */
    public function apacheConfig();
}
