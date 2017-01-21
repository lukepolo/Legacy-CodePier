<?php

namespace App\Services\Buoys;

use App\Models\Server\Server;
use App\Traits\Buoys\BuoyTrait;
use App\Contracts\Buoys\BuoyContract;
use App\Services\Systems\SystemService;

class ElasticsearchBuoy implements BuoyContract
{
    use BuoyTrait;

    /**
     * @buoy-title Elasticsearch
     * @buoy-enabled 1
     *
     * @param Server $server
     * @param array $ports
     * @param array ...$options
     *
     * @buoy-ports Transport Client:9200:9200
     * @buoy-ports Node Client:9300:9300
     * @buoy-options memory:2g
     * @buoy-option-desc-memory Minimum is 512 mb , anything lower than that will cause it not able to start
     */
    public function install(Server $server, $ports = [], ...$options)
    {
        list($memory) = $options;

        $this->remoteTaskService->run('sysctl -w vm.max_map_count=262144');
        $this->remoteTaskService->run('docker pull elasticsearch');

        $this->remoteTaskService->run("docker run -d -e ES_JAVA_OPTS=\"-Xms$memory -Xmx$memory\" -p $ports[0]:9200 -p $ports[1]:9300 elasticsearch");

        $this->openPorts($server, $ports, 'elasticsearch');

        $this->serverService->getService(SystemService::FIREWALL, $server)->addFirewallRule();
    }

    /**
     * When a buoy is set to a domain we must gather the web config.
     * @param Server $server
     * return string
     */
    public function nginxConfig(Server $server)
    {
    }

    /**
     * When a buoy is set to a domain we must gather the web config.
     * @param Server $server
     * return string
     */
    public function apacheConfig(Server $server)
    {
    }
}
