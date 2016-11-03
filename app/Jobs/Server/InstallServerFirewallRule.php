<?php

namespace App\Jobs\Server;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Models\Server\ServerFirewallRule;
use App\Services\Systems\SystemService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class InstallServerFirewallRule implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $serverFirewallRule;

    /**
     * InstallServerFirewallRule constructor.
     *
     * @param ServerFirewallRule $serverFirewallRule
     */
    public function __construct(ServerFirewallRule $serverFirewallRule)
    {
        $this->serverFirewallRule = $serverFirewallRule;
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     */
    public function handle(ServerService $serverService)
    {
        $this->runOnServer(function () use ($serverService) {
            $serverService->getService(SystemService::FIREWALL, $this->serverFirewallRule->server)->addFirewallRule($this->serverFirewallRule);
        });
    }
}
