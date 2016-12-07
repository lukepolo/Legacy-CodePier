<?php

namespace App\Jobs\Server;

use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Server\Provider\ServerProvider;
use App\Events\Server\ServerProvisionStatusChanged;
use App\Contracts\Server\ServerServiceContract as ServerService;

class CreateServer implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

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
     * @param \App\Services\Server\ServerService | ServerService $serverService
     *
     * @throws \Exception
     */
    public function handle(ServerService $serverService)
    {
        event(new ServerProvisionStatusChanged($this->server, 'Creating Server', 0));

        /* @var Server $server */
        $serverService->create($this->serverProvider, $this->server);

        dispatch(
            (new CheckServerStatus($this->server, true))->delay(30)
        );
    }
}
