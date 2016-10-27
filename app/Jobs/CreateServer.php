<?php

namespace App\Jobs;

use App\Contracts\Server\ServerServiceContract;
use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Events\Server\ServerProvisionStatusChanged;
use App\Models\Server\Provider\ServerProvider;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class CreateServer.
 */
class CreateServer implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, DispatchesJobs;

    protected $server;
    protected $options;
    protected $serverProvider;

    /**
     * Create a new job instance.
     *
     * @param ServerProvider $serverProvider
     * @param Server $server
     */
    public function __construct(ServerProvider $serverProvider, Server $server)
    {
        $this->server = $server;
        $this->serverProvider = $serverProvider;
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\ServerService | ServerServiceContract $serverService
     *
     * @throws \Exception
     */
    public function handle(ServerService $serverService)
    {
        event(new ServerProvisionStatusChanged($this->server, 'Creating Server', 0));

        /* @var Server $server */
        $serverService->create($this->serverProvider, $this->server);

        $this->dispatch(new CheckServerStatus($this->server, true));
    }
}
