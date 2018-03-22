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

class RefreshServerFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $server;

    public $tries = 1;
    public $timeout = 60;

    /**
     * Create a new job instance.
     * @param Server $server
     * @param Command $siteCommand
     */
    public function __construct(Server $server, Command $siteCommand = null)
    {
        $this->server = $server;
        $this->makeCommand($server, $server, $siteCommand, 'Refreshing');
    }

    /**
     * Execute the job.
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @throws \Exception
     */
    public function handle(ServerService $serverService)
    {
        $this->runOnServer(function () use ($serverService) {
            foreach ($this->server->files as $file) {
                $file->content = $serverService->getFile($this->server, $file->file_path);
                $file->save();
            }
        });
    }
}
