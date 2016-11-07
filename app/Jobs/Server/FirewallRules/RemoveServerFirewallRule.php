<?php

namespace App\Jobs\Server\FirewallRules;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Models\Server\ServerFirewallRule;
use App\Services\Systems\SystemService;
use App\Traits\ServerCommandTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveServerFirewallRule implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $serverFirewallRule;

    /**
     * InstallServerFirewallRule constructor.
     *
     * @param ServerFirewallRule $serverFirewallRule
     */
    public function __construct(ServerFirewallRule $serverFirewallRule)
    {
        $this->makeCommand($serverFirewallRule);
        $this->serverFirewallRule = $serverFirewallRule;
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(ServerService $serverService)
    {
        $this->runOnServer(function () use ($serverService) {
            $serverService->getService(SystemService::FIREWALL, $this->serverFirewallRule->server)->removeFirewallRule($this->serverFirewallRule);
        });

        if ($this->wasSuccessful()) {
            $this->serverFirewallRule->unsetEventDispatcher();
            $this->serverFirewallRule->delete();
        }

        return $this->remoteResponse();
    }
}
