<?php

namespace App\Jobs\Server;

use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Server\ServerServiceContract;

class CheckServerStatus implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

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
    public function handle(ServerServiceContract $serverService)
    {
        $serverStatus = $serverService->getStatus($this->server, true);

        if ($this->provision) {
            $serverProvider = $this->server->serverProvider;

            $serverProviderClass = new $serverProvider->provider_class($serverProvider->provider_name);

            if ($serverProviderClass->readyForProvisioningStatus() == $serverStatus) {
                $serverService->saveInfo($this->server);
                dispatch(new CheckSshConnection($this->server));
            } else {
                dispatch((new self($this->server, $this->provision))->delay(10));
            }
        }
    }
}
