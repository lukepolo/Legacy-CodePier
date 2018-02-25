<?php

namespace App\Jobs\Server;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Models\Command;
use App\Models\File;
use App\Models\Server\Server;
use App\Traits\ServerCommandTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateServerFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $file;
    private $server;

    public $tries = 1;
    public $timeout = 60;

    /**
     * Create a new job instance.
     *
     * @param Server  $server
     * @param File    $file
     * @param Command $siteCommand
     */
    public function __construct(Server $server, File $file, Command $siteCommand = null)
    {
        $this->server = $server;
        $this->file = $file;
        $this->makeCommand($server, $file, $siteCommand, 'Updating');
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     *
     * @throws \Exception
     */
    public function handle(ServerService $serverService)
    {
        $this->runOnServer(function () use ($serverService) {
            $user = 'root';

            if (str_contains($this->file->file_path, '~/') || str_contains($this->file->file_path, 'codepier')) {
                $user = 'codepier';
            }

            $serverService->saveFile($this->server, $this->file->file_path, $this->file->content, $user);
        });
    }
}
