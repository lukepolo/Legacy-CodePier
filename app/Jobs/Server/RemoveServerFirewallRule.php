<?php

namespace App\Jobs\Server;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Exceptions\Traits\ServerErrorTrait;
use App\Models\Server\ServerFirewallRule;
use App\Services\Systems\SystemService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RemoveServerFirewallRule implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerErrorTrait;

    private $serverFirewallRule;

    /**
     * RemoveServerFirewallRule constructor.
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(ServerService $serverService)
    {
        $this->runOnServer(function () use ($serverService) {
            $serverService->getService(SystemService::FIREWALL, $this->serverFirewallRule->server)->removeFirewallRule($this->serverFirewallRule);
        });

        if($this->wasSuccessful()) {
            $this->serverFirewallRule->delete();
        }

        return $this->remoteResponse();
    }
}
