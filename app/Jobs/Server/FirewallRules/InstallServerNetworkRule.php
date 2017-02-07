<?php

namespace App\Jobs\Server\FirewallRules;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Exceptions\ServerCommandFailed;
use App\Services\Systems\SystemService;
use App\Models\Server\ServerNetworkRule;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Server\ServerServiceContract as ServerService;

class InstallServerNetworkRule implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $serverNetworkRule;

    public $tries = 1;
    public $timeout = 60;

    /**
     * Create a new job instance.
     *
     * @param ServerNetworkRule $serverNetworkRule
     */
    public function __construct(ServerNetworkRule $serverNetworkRule)
    {
        dd('TODO - server network rules');
        $this->makeCommand($serverNetworkRule);
        $this->serverNetworkRule = $serverNetworkRule;
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
            $serverService->getService(SystemService::FIREWALL, $this->serverNetworkRule->server)->addServerNetworkRule($this->serverNetworkRule->server->ip);
        });
    }
}
