<?php

namespace App\Jobs\Server;

use App\Contracts\Server\ServerServiceContract;
use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class CheckServerStatus.
 */
class CheckServerStatus implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, DispatchesJobs;

    protected $server;
    protected $provision;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Server\Server $server
     * @param bool $provision
     */
    public function __construct(Server $server, $provision = false)
    {
        $this->server = $server;
        $this->provision = $provision;
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\ServerService | ServerServiceContract $serverService
     */
    public function handle(ServerService $serverService)
    {
        $serverStatus = $serverService->getStatus($this->server, true);

        if ($this->provision) {
            $serverProvider = $this->server->serverProvider;

            $serverProviderClass = new $serverProvider->provider_class($serverProvider->provider_name);

            if ($serverProviderClass->readyForProvisioningStatus() == $serverStatus) {
                $serverService->saveInfo($this->server);
                $this->dispatch(new CheckSshConnection($this->server));
            } else {
                $this->dispatch((new self($this->server, $this->provision))->delay(10));
            }
        }
    }
}
