<?php

namespace App\Services\Buoys;

use App\Traits\Buoys\BuoyTrait;
use App\Contracts\Buoys\BuoyContract;

class ElasticsearchBuoy implements BuoyContract
{
    use BuoyTrait;

    /**
     * @buoy-title Elasticsearch
     * @buoy-enabled 1
     *
     * @description Elasticsearch is a distributed, RESTful search and analytics engine capable of solving a growing number of use cases. As the heart of the Elastic Stack, it centrally stores your data so you can discover the expected and uncover the unexpected.
     * @category Services
     *
     * @param array $ports
     * @param array $options
     *
     * @buoy-ports Node Client:9300:9300
     * @buoy-ports Transport Client:9200:9200
     * @buoy-options memory:2g
     * @buoy-option-desc-memory Minimum is 512 mb , anything lower than that will cause it not able to start
     * @return array Conatiner Ids
     */
    public function install($ports, $options)
    {
        $memory = $options['memory'];

        $this->remoteTaskService->run('sysctl -w vm.max_map_count=262144');
        $this->remoteTaskService->run('docker pull elasticsearch');

        $this->remoteTaskService->run("docker run -d -e ES_JAVA_OPTS=\"-Xms$memory -Xmx$memory\" -p $ports[0]:9300 -p $ports[1]:9200 elasticsearch");

        $this->getContainerId();

        $this->openPorts($this->server, $ports, 'elasticsearch');

        return $this->containerIds;
    }

    /**
     * When a buoy is set to a domain we must gather the web config.
     * return string.
     */
    public function nginxConfig()
    {
    }

    /**
     * When a buoy is set to a domain we must gather the web config.
     * return string.
     */
    public function apacheConfig()
    {
    }
}
