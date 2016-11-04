<?php

namespace App\Jobs\Server;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Models\Server\ServerCronJob;
use App\Models\Site\Site;
use App\Models\Site\SiteFile;
use App\Traits\ServerCommandTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateServerFile implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $siteFile;

    /**
     * Create a new job instance.
     * @param SiteFile $siteFile
     */
    public function __construct(SiteFile $siteFile)
    {
        $this->siteFile = $siteFile;
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
            $serverService->saveFile($this->siteFile->site->server, $this->siteFile->file_path, $this->siteFile->content, 'codepier');
        });

        return $this->remoteResponse();
    }
}
