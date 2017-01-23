<?php

namespace App\Contracts\Buoys;

interface BuoyContract
{
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
     * NOTE : these should be in alphabetical order
     * @buoy-ports Node Client:9300:9300
     * @buoy-ports Transport Client:9200:9200
     *
     * @buoy-options memory:2g
     * @buoy-option-desc-memory Minimum is 512 mb , anything lower than that will cause it not able to start
     *
     * @return array Container Ids
     */
    public function install($ports = [], $options);

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
