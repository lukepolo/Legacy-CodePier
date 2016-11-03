<?php

namespace App\Jobs\Server;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Models\Server\Provider\ServerProvider;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class GetServerOptions.
 */
class GetServerOptions implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $server;

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     */
    public function handle(ServerService $serverService)
    {
        foreach (ServerProvider::all() as $server) {
            $serverService->getServerOptions($server);
        }
    }
}
