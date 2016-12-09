<?php

namespace App\Jobs\Server\FirewallRules;

use Illuminate\Bus\Queueable;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Exceptions\ServerCommandFailed;
use App\Services\Systems\SystemService;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Server\ServerFirewallRule;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Server\ServerServiceContract as ServerService;

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
     * @throws ServerCommandFailed
     */
    public function handle(ServerService $serverService)
    {
        $this->runOnServer(function () use ($serverService) {
            $serverService->getService(SystemService::FIREWALL, $this->serverFirewallRule->server)->removeFirewallRule($this->serverFirewallRule);
        });

        if ($this->wasSuccessful()) {
            $this->serverFirewallRule->unsetEventDispatcher();
            $this->serverFirewallRule->delete();
            if (\App::runningInConsole()) {
                throw new ServerCommandFailed($this->getCommandErrors());
            }
        }

        return $this->remoteResponse();
    }
}
