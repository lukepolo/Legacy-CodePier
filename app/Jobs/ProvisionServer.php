<?php

namespace App\Jobs;

use App\Contracts\Server\ServerServiceContract;
use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Models\UserServer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProvisionServer extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $userServer;

    /**
     * Create a new job instance.
     */
    public function __construct(UserServer $userServer)
    {
        $this->userServer = $userServer;
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\ServerService | ServerServiceContract $serverService
     */
    public function handle(ServerService $serverService)
    {
        $serverService->provision($this->userServer);
    }
}
