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

class RemoveServerFirewallRule implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $server;
    private $firewallRule;

    public $tries = 1;
    public $timeout = 60;

    /**
     * InstallServerFirewallRule constructor.
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
        $sitesCount = $this->firewallRule->sites->count();

        if (! $sitesCount) {
            $this->runOnServer(function () use ($serverService) {
                $serverService->getService(SystemService::FIREWALL, $this->server)->removeFirewallRule($this->firewallRule);
            });

            if (! $this->wasSuccessful()) {
                throw new ServerCommandFailed($this->getCommandErrors());
            }

            $this->server->firewallRules()->detach($this->firewallRule->id);

            $this->firewallRule->load('servers');
            if ($this->firewallRule->servers->count() == 0) {
                $this->firewallRule->delete();
            }
        } else {
            $this->updateServerCommand(0, 'Sites that are on this server using this firewall rule', false);
        }
    }
}
