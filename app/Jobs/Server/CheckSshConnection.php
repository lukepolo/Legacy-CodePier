<?php

namespace App\Jobs\Server;

use App\Contracts\Server\ServerServiceContract;
use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Events\Server\ServerProvisionStatusChanged;
use App\Events\Server\ServerSshConnectionFailed;
use App\Models\Server\Server;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckSshConnection implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $server;

    public $tries = 1;
    public $timeout = 10;

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
            broadcast(new ServerProvisionStatusChanged($this->server, 'Queue for Provisioning', 0));

            dispatch(
                (new ProvisionServer($this->server))
                    ->onQueue(config('queue.channels.server_provisioning'))
            );
        } else {
            $this->tryAgain();
        }
    }

    public function failed()
    {
        $this->tryAgain();
    }

    private function tryAgain()
    {
        if ($this->server->created_at->addMinutes(10) > Carbon::now()) {
            dispatch(
                (new self($this->server))
                    ->delay(20)
                    ->onQueue(config('queue.channels.server_commands'))
            );

            return;
        }

        event(
            new ServerSshConnectionFailed($this->server, 'Cannot connect to server. Server provisioning failed.')
        );
    }
}
