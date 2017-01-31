<?php

namespace App\Jobs\Server;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Server\Provider\ServerProvider;
use App\Contracts\Server\ServerServiceContract as ServerService;

class GetServerOptions implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $server;

    public $tries = 3;
    public $timeout = 60;

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
