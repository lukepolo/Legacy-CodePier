<?php

namespace App\Jobs\Server;

use App\Models\Command;
use App\Models\Site\Site;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;

class InstallPublicKey implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $site;
    private $server;

    public $tries = 1;
    public $timeout = 30;

    /**
     * Create a new job instance.
     *
     * @param Server $server
     * @param Site $site
     * @param Command|null $command
     */
    public function __construct(Server $server, Site $site, Command $command = null)
    {
        $this->site = $site;
        $this->server = $server;

        $this->makeCommand($server, $site, $command, 'Installing sites public key for deployments');
    }

    /**
     * Execute the job.
     * @param \App\Services\RemoteTaskService | RemoteTaskService $remoteTaskService
     * @throws \Exception
     */
    public function handle(RemoteTaskService $remoteTaskService)
    {
        $this->runOnServer(function () use ($remoteTaskService) {
            $remoteTaskService->saveSshKeyToServer($this->site, $this->server);
        });
    }
}
