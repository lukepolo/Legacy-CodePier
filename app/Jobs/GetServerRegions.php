<?php

namespace App\Jobs;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Models\ServerProvider;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class GetServerRegions
 * @package App\Jobs
 */
class GetServerRegions implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $server;

    /**
     * Execute the job.
     * @param \App\Services\Server\ServerService | ServerService $serverService
     */
    public function handle(ServerService $serverService)
    {
        foreach (ServerProvider::all() as $server) {
            $serverService->getServerRegions($server);
        }
    }
}
