<?php

namespace App\Jobs\Server;

use App\Contracts\Server\ServerServiceContract;
use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Events\Server\ServerProvisionStatusChanged;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class CheckServerStatus.
 */
class CheckSshConnection implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, DispatchesJobs;

    protected $server;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Server\Server $server
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\ServerService | ServerServiceContract $serverService
     */
    public function handle(ServerService $serverService)
    {
        if ($serverService->testSshConnection($this->server)) {
            event(new ServerProvisionStatusChanged($this->server, 'Queue for Provisioning', 0));

            dispatch(new ProvisionServer($this->server));
            //            ->onQueue('server_provision'));
        } else {
            $this->dispatch((new self($this->server))->delay(10));
        }
    }
}
