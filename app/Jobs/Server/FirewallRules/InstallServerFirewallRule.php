<?php

namespace App\Jobs\Server\FirewallRules;

use App\Models\Command;
use App\Models\FirewallRule;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Exceptions\ServerCommandFailed;
use App\Services\Systems\SystemService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Server\ServerServiceContract as ServerService;

class InstallServerFirewallRule implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $server;
    private $firewallRule;

    public $tries = 1;
    public $timeout = 60;

    /**
     * InstallServerFirewallRule constructor.
     *
     * @param Server $server
     * @param FirewallRule $firewallRule
     * @param Command $siteCommand
     */
    public function __construct(Server $server, FirewallRule $firewallRule, Command $siteCommand = null)
    {
        $this->server = $server;
        $this->firewallRule = $firewallRule;
        $this->makeCommand($server, $firewallRule, $siteCommand);
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @return \Illuminate\Http\JsonResponse
     * @throws ServerCommandFailed
     */
    public function handle(ServerService $serverService)
    {
        if ($this->server->firewallRules
            ->where('port', $this->firewallRule->port)
            ->where('from_ip', $this->firewallRule->from_ip)
            ->count()
            ||
            $this->server->firewallRules->keyBy('id')->get($this->firewallRule->id)
        ) {
            $this->updateServerCommand(0, 'Sever already has firewall rule : '.$this->firewallRule->port.' from ip '.$this->firewallRule->from_ip);
        } else {
            $this->runOnServer(function () use ($serverService) {
                $serverService->getService(SystemService::FIREWALL, $this->server)->addFirewallRule($this->firewallRule);
            });

            $this->server->firewallRules()->save($this->firewallRule);
        }
    }
}
