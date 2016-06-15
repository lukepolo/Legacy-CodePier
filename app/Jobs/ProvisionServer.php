<?php

namespace App\Jobs;

use App\Contracts\Server\ServerServiceContract;
use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Models\Server;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class ProvisionServer
 * @package App\Jobs
 */
class ProvisionServer extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $server;

    /**
     * Create a new job instance.
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
        $serverService->provision($this->server);

        dispatch(new CreateSite($this->server));
    }
}
