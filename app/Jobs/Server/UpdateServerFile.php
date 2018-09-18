<?php

namespace App\Jobs\Server;

use App\Models\File;
use App\Models\Command;
use App\Models\Site\Site;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Contracts\Site\SiteServiceContract as SiteService;
use App\Contracts\Server\ServerServiceContract as ServerService;

class UpdateServerFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $file;
    private $server;
    private $site;
    private $shouldFlushLaravelConfigCache;

    public $tries = 1;
    public $timeout = 60;

    /**
     * Create a new job instance.
     * @param Server $server
     * @param File $file
     * @param Command $siteCommand
     * @param Site|null $site
     * @param boolean|null $shouldFlushLaravelConfigCache
     */
    public function __construct(Server $server, File $file, Command $siteCommand = null, $site = null, $shouldFlushLaravelConfigCache = false)
    {
        $this->shouldFlushLaravelConfigCache = $shouldFlushLaravelConfigCache;
        $this->server = $server;
        $this->file = $file;
        $this->site = $site;

        $this->makeCommand($server, $file, $siteCommand, 'Updating');
    }

    /**
     * Execute the job.
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @throws \Exception
     */
    public function handle(ServerService $serverService, SiteService $siteService)
    {
        $this->runOnServer(function () use ($serverService, $siteService) {
            $user = 'root';

            if (str_contains($this->file->file_path, '~/') || str_contains($this->file->file_path, 'codepier')) {
                $user = 'codepier';
            }

            $serverService->saveFile($this->server, $this->file->file_path, $this->file->content, $user);

            if ($this->shouldFlushLaravelConfigCache) {
                $siteService->cacheLaravelConfig($this->site, $this->server);
            }
        });
    }
}
