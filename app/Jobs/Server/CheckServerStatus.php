<?php

namespace App\Jobs\Server;

use Carbon\Carbon;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Contracts\Server\ServerServiceContract;
use App\Events\Server\ServerSshConnectionFailed;

class CheckServerStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $server;
    protected $provision;

    public $tries = 1;
    public $timeout = 15;

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
     * @throws \Exception
     */
    public function handle(ServerServiceContract $serverService)
    {
        $serverStatus = $serverService->getStatus($this->server, true);

        if ($this->provision) {
            $serverProvider = $this->server->serverProvider;

            $serverProviderClass = new $serverProvider->provider_class($serverProvider->provider_name);

            if ($serverProviderClass->readyForProvisioningStatus() == $serverStatus) {
                $serverService->saveInfo($this->server);
                dispatch(
                    (new CheckSshConnection($this->server))
                        ->onQueue(config('queue.channels.server_commands'))
                );
            } else {
                if ($this->server->created_at->addMinutes(10) > Carbon::now()) {
                    dispatch(
                        (new self($this->server, $this->provision))
                            ->delay(10)
                            ->onQueue(config('queue.channels.server_commands'))
                    );

                    return;
                }

                event(
                   new ServerSshConnectionFailed($this->server, 'Server failed to create.')
                );
            }
        }
    }
}
