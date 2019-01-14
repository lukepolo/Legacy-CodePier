<?php

namespace App\Jobs\Server\Daemons;

use App\Models\Daemon;
use App\Models\Command;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Services\Systems\SystemService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Contracts\Server\ServerServiceContract as ServerService;

class RemoveServerDaemon implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $server;
    private $daemon;
    private $forceRemove;

    public $tries = 1;
    public $timeout = 60;

    /**
     * InstallServerWorker constructor.
     * @param Server $server
     * @param Daemon $daemon
     * @param Command $siteCommand
     * @param bool $forceRemove
     */
    public function __construct(Server $server, Daemon $daemon, Command $siteCommand = null, $forceRemove = false)
    {
        $this->server = $server;
        $this->daemon = $daemon;
        $this->forceRemove = $forceRemove;
        $this->makeCommand($server, $daemon, $siteCommand, 'Removing');
    }

    /**
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @throws \Exception
     */
    public function handle(ServerService $serverService)
    {
        $sitesCount = $this->daemon->sites->count();

        if (! $this->server->daemons->keyBy('id')->get($this->daemon->id)) {
            $this->updateServerCommand(0, 'Sever does not have daemon installed');
            return;
        }

        if ($this->forceRemove || ! $sitesCount) {
            $this->runOnServer(function () use ($serverService) {
                $serverService->getService(SystemService::WORKERS, $this->server)->removeDaemon($this->daemon);
            });

            if ($this->wasSuccessful()) {
                $this->server->daemons()->detach($this->daemon->id);

                $this->daemon->load('servers');
                if ($this->daemon->servers->count() == 0) {
                    $this->daemon->delete();
                }
            }
        } else {
            $this->updateServerCommand(0, 'Sites that are on this server using this daemon', false);
        }
    }
}
