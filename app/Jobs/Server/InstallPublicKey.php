<?php

namespace App\Jobs\Server;

use App\Models\Server\Server;
use App\Models\Site\Site;
use Illuminate\Bus\Queueable;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;

class InstallPublicKey implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $site;
    private $server;

    public $tries = 1;
    public $timeout = 30;

    /**
     * Create a new job instance.
     *
     * @param Server $server
     * @param Site $site
     */
    public function __construct(Server $server, Site $site)
    {
        $this->site = $site;
        $this->server = $server;
    }

    /**
     * Execute the job.
     * @param \App\Services\RemoteTaskService | RemoteTaskService $remoteTaskService
     */
    public function handle(RemoteTaskService $remoteTaskService)
    {
        $this->runOnServer(function () use ($remoteTaskService) {
            $remoteTaskService->saveSshKeyToServer($this->site, $this->server);
        });
    }
}
