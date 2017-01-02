<?php

namespace App\Jobs\Server;

use App\Models\Server\Server;
use App\Models\Site\SiteFile;
use Illuminate\Bus\Queueable;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Server\ServerServiceContract as ServerService;

class UpdateServerFile implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $server;
    private $siteFile;

    /**
     * Create a new job instance.
     * @param Server $server
     * @param SiteFile $siteFile
     */
    public function __construct(Server $server, SiteFile $siteFile)
    {
        $this->server = $server;
        $this->siteFile = $siteFile;
        $siteFile->server_id = $server->id;
        $this->makeCommand($siteFile, 'site_id');
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

            if(str_contains($this->siteFile->file_path, '~/') || str_contains($this->siteFile->file_path, 'codepier')) {
                $user = 'codepier';
            }

            $serverService->saveFile($this->server, $this->siteFile->file_path, $this->siteFile->content, $user);
        });

        return $this->remoteResponse();
    }
}
