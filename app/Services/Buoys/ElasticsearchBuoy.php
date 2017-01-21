<?php

namespace App\Services\Buoys;

use App\Models\FirewallRule;
use App\Models\Server\Server;
use App\Traits\Buoys\BuoyTrait;
use App\Contracts\Buoys\BuoyContract;
use App\Services\Systems\SystemService;

class ElasticsearchBuoy implements BuoyContract
{
    use BuoyTrait;

    /**
     * @param Server $server
     * @param array ...$parameters
     *
     * @buoy-param $memory = 2g
     */
    public function install(Server $server, ...$parameters)
    {
        list($memory) = $parameters;

        $this->remoteTaskService->run('sysctl -w vm.max_map_count=262144');
        $this->remoteTaskService->run('docker pull elasticsearch');

        $port = '9200';
        $type = 'tcp';

        $this->remoteTaskService->run("docker run -d -e ES_JAVA_OPTS=\"-Xms$memory -Xmx$memory\" -p $port:$port -p 9300:9300 elasticsearch");

        if (! $server->firewallRules
            ->where('port', $port)
            ->where('from_ip', null)
            ->where('type', $type)
            ->count()
        ) {
            $firewallRule = FirewallRule::create([
                'port' => $port,
                'type' => $type,
                'description' => 'Elasticserach',
            ]);

            $server->firewallRules()->save($firewallRule);
        }

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
