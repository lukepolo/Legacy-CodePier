<?php

namespace App\Jobs\Server;

use App\Models\Command;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Contracts\Server\ServerServiceContract as ServerService;

class BackupDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $title;
    private $server;
    private $databases;
    private $siteCommand;

    public $tries = 1;
    public $timeout = 300;

    /**
     * Create a new job instance.
     *
     * @param Server $server
     * @param array $databases
     * @param Command|null $siteCommand
     */
    public function __construct(Server $server, $databases = [], Command $siteCommand = null)
    {
        $this->server = $server;
        $this->databases = $databases;
        $this->siteCommand = $siteCommand;
        $this->makeCommand($server, $server, $siteCommand, 'Backing up databases');

        $this->title = '';

        if(!empty($siteCommand)) {
            $this->title = $siteCommand->site->name;
        }
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @throws \Exception
     */
    public function handle(ServerService $serverService)
    {
        $this->runOnServer(function() use($serverService) {
            $backup = $serverService->backupDatabases($this->server, empty($this->databases) ?: $this->databases, $this->title);
            $this->server->backups()->save($backup);
            if(!empty($this->siteCommand)) {
                $this->siteCommand->site->backups()->save($backup);
            }
        });
    }
}
