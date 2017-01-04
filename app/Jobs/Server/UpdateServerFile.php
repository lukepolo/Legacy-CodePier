<?php

namespace App\Jobs\Server;

use App\Models\File;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Server\ServerServiceContract as ServerService;

class UpdateServerFile implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $file;
    private $server;

    /**
     * Create a new job instance.
     * @param Server $server
     * @param File $file
     */
    public function __construct(Server $server, File $file)
    {
        $this->server = $server;
        $this->file = $file;

        $this->makeCommand($file, $server->id);
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     *
     * @return \Illuminate\Http\JsonResponse
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

        return $this->remoteResponse();
    }
}
