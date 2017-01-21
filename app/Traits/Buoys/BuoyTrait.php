<?php

namespace App\Traits\Buoys;

use App\Models\FirewallRule;
use App\Models\Server\Server;
use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;

trait BuoyTrait
{
    private $serverService;
    private $remoteTaskService;
    private $domainable = false;

    /**
     * BuoyTrait constructor.
     * @param \App\Services\Server\ServerService |ServerService $serverService
     * @param \App\Services\RemoteTaskService | RemoteTaskService $remoteTaskService
     */
    public function __construct(ServerService $serverService, RemoteTaskService $remoteTaskService)
    {
        $this->serverService = $serverService;
        $this->remoteTaskService = $remoteTaskService;
    }

    /**
     * @param Server $server
     * @param array $ports
     * @param $container
     */
    public function openPorts(Server $server, $ports, $container)
    {
        foreach ($ports as $port) {
            if (! $server->firewallRules
                ->where('port', $ports[0])
                ->where('from_ip', null)
                ->where('type', 'both')
                ->count()
            ) {
                $firewallRule = FirewallRule::create([
                    'port' => $port,
                    'type' => 'both',
                    'description' => ucwords($container),
                ]);

                $server->firewallRules()->save($firewallRule);
            }
        }
    }
}
