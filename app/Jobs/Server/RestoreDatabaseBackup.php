<?php

namespace App\Jobs\Server;

use App\Models\Backup;
use Illuminate\Bus\Queueable;
use App\Models\Server\Server;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Contracts\Server\ServerServiceContract as ServerService;

class RestoreDatabaseBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    public $server;
    public $backup;

    /**
     * Create a new job instance.
     *
     * @param Server $server
     * @param Backup $backup
     */
    public function __construct(Server $server, Backup $backup)
    {
        $this->server = $server;
        $this->backup = $backup;
        $this->makeCommand($server, $backup, null, 'Restoring Backup');
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\ServerService|ServerService $serverService
     * @throws \Exception
     */
    public function handle(ServerService $serverService)
    {
        $this->runOnServer(function () use ($serverService) {
            $serverService->restoreDatabases($this->server, $this->backup);
        });
    }
}
