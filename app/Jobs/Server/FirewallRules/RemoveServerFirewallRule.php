<?php

namespace App\Jobs\Server\FirewallRules;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Models\Command;
use App\Models\FirewallRule;
use App\Models\Server\Server;
use App\Services\Systems\SystemService;
use App\Traits\ServerCommandTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveServerFirewallRule implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $server;
    private $firewallRule;

    public $tries = 1;
    public $timeout = 60;

    /**
     * RemoveServerFirewallRule constructor.
     *
     * @param Server       $server
     * @param FirewallRule $firewallRule
     * @param Command      $siteCommand
     */
    public function __construct(Server $server, FirewallRule $firewallRule, Command $siteCommand = null)
    {
        $this->server = $server;
        $this->firewallRule = $firewallRule;
        $this->makeCommand($server, $firewallRule, $siteCommand, 'Closing');
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     *
     * @throws \Exception
     */
    public function handle(ServerService $serverService)
    {
        $sitesCount = $this->firewallRule->sites->count();

        if (! $sitesCount) {
            $this->runOnServer(function () use ($serverService) {
                $serverService->getService(SystemService::FIREWALL, $this->server)->removeFirewallRule($this->firewallRule);

                switch ($this->server->type) {
                    case SystemService::FULL_STACK_SERVER:
                        $serverService->restartDatabase($this->server);
                        $serverService->restartWorkers($this->server);
                        break;
                    case SystemService::DATABASE_SERVER:
                        $serverService->restartDatabase($this->server);
                        break;
                    case SystemService::WORKER_SERVER:
                        $serverService->restartWorkers($this->server);
                        break;
                }
            });

            if ($this->wasSuccessful()) {
                $this->server->firewallRules()->detach($this->firewallRule->id);

                $this->firewallRule->load('servers');
                if (0 == $this->firewallRule->servers->count()) {
                    $this->firewallRule->delete();
                }
            }
        } else {
            $this->updateServerCommand(0, 'Sites that are on this server using this firewall rule', false);
        }
    }
}
