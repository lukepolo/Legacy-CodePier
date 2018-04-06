<?php

namespace App\Jobs\Server;

use Illuminate\Bus\Queueable;
use App\Models\Server\Server;
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
    public $backup;

    public $tries = 1;
    public $timeout = 90;

    /**
     * Create a new job instance.
     *
     * @param Server $server
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
        $this->makeCommand($server, $server, null, 'Backup Databases');
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\ServerService|ServerService $serverService
     * @throws \Exception
     */
    public function handle(ServerService $serverService)
    {
        $backupable = [
            'MySQL',
            'MariaDB',
            'PostgreSQL',
            'MongoDB'
        ];

        if (isset($this->server->server_features['DatabaseService'])) {
            $databases = collect($this->server->server_features['DatabaseService'])->filter(function ($databaseFeature) {
                return $databaseFeature['enabled'];
            })->keys()->filter(function ($databaseFeature) use ($backupable) {
                return in_array($databaseFeature, $backupable);
            });

            return $this->runOnServer(function () use ($serverService, $databases) {
                foreach ($databases as $database) {
                    $backup = $serverService->backupDatabases($this->server, $database);
                    $this->server->backups()->save($backup);
                }
            });
        } else {
            $this->updateServerCommand(0, [
                ['Your server does not have any databases installed']
            ], false);
        }
    }
}
