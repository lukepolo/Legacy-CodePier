<?php

namespace App\Jobs\Server\FirewallRules;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Models\Server\ServerNetworkRule;
use App\Services\Systems\SystemService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveServerNetworkRule implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $serverNetworkRule;

    /**
     * Create a new job instance.
     *
     * @param ServerNetworkRule $serverNetworkRule
     */
    public function __construct(ServerNetworkRule $serverNetworkRule)
    {
        $this->makeCommand($serverNetworkRule);
        $this->serverNetworkRule = $serverNetworkRule;
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
            $serverService->getService(SystemService::FIREWALL, $this->serverNetworkRule->server)->removeServerNetworkRule($this->serverNetworkRule->server->ip);
        });

        if (! $this->wasSuccessful()) {
            $this->serverNetworkRule->unsetEventDispatcher();
            $this->serverNetworkRule->delete();
        }

        return $this->remoteResponse();
    }
}
