<?php

namespace App\Jobs;

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
     * @return void
     */
    public function __construct(Server $server, Backup $backup)
    {
        $this->server = $server;
        $this->backup = $backup;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ServerService $serverService)
    {
        return $serverService->restoreDatabases($this->server, $this->backup);
    }
}
