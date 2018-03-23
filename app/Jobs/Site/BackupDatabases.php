<?php

namespace App\Jobs\Site;

use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Contracts\Server\ServerServiceContract as ServerService;

class BackupDatabases implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    public $server;

    public $tries = 1;
    public $timeout = 60;

    /**
     * Create a new job instance.
     *
     * @param Server $server
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * Execute the job.
     */
    public function handle(ServerService $serverService)
    {
        $backupable = [
            'MySQL',
            'MariaDB',
            'PostgreSQL',
            'MongoDB'
        ];

        $databases = collect($this->server->server_features['DatabaseService'])->filter(function ($databaseFeature) {
            return $databaseFeature['enabled'];
        })->keys()->filter(function ($databaseFeature) use ($backupable) {
            return in_array($databaseFeature, $backupable);
        });

        $this->runOnServer(function () use ($serverService, $databases) {
            foreach ($databases as $database) {
                $backup = $serverService->backupDatabases($this->server, $database);
                $this->server->backups()->save($backup);
            }
        });
    }
}
