<?php

namespace App\Jobs\Server\FirewallRules;

use App\Traits\ServerCommandTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Exceptions\ServerCommandFailed;
use App\Services\Systems\SystemService;
use App\Models\Server\ServerNetworkRule;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Server\ServerServiceContract as ServerService;

class RemoveServerNetworkRule implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $serverNetworkRule;

    /**
     * Create a new job instance.
     *
     * @param ServerNetworkRule $serverNetworkRule
     */
    public function __construct(ServerNetworkRule $serverNetworkRule)
    {
        $this->serverNetworkRule = $serverNetworkRule;
        $this->makeCommand($serverNetworkRule->server, $serverNetworkRule);
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
            $serverService->getService(SystemService::FIREWALL, $this->serverNetworkRule->server)->removeServerNetworkRule($this->serverNetworkRule->server->ip);
        });

        if (! $this->wasSuccessful()) {
            if (\App::runningInConsole()) {
                throw new ServerCommandFailed($this->getCommandErrors());
            }
        } else {
            $this->serverNetworkRule->delete();
        }

        return $this->remoteResponse();
    }
}
