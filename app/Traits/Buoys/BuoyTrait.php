<?php

namespace App\Traits\Buoys;

use App\Models\FirewallRule;
use App\Models\Server\Server;
use App\Jobs\Server\FirewallRules\InstallServerFirewallRule;
use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;

trait BuoyTrait
{
    private $server;
    private $serverService;
    private $remoteTaskService;
    private $domainable = false;

    /**
     * BuoyTrait constructor.
     * @param \App\Services\Server\ServerService |ServerService $serverService
     * @param \App\Services\RemoteTaskService | RemoteTaskService $remoteTaskService
     * @param Server $server
     */
    public function __construct(ServerService $serverService, RemoteTaskService $remoteTaskService, Server $server)
    {
        $this->serverService = $serverService;
        $this->remoteTaskService = $remoteTaskService;
        $this->server = $server;

        $this->remoteTaskService->ssh($server);
    }

    /**
     * @param Server $server
     * @param array $ports
     * @param $container
     */
    public function openPorts(Server $server, $ports, $container)
    {
        foreach ($ports as $port) {
            $firewallRule = FirewallRule::create([
                'port' => $port,
                'type' => 'tcp',
                'description' => ucwords($container),
            ]);

            dispatch(new InstallServerFirewallRule($server, $firewallRule));
        }
    }
}
