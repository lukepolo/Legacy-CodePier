<?php

namespace App\Jobs\Server\Workers;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Models\Command;
use App\Models\Server\Server;
use App\Models\Worker;
use App\Services\Systems\SystemService;
use App\Traits\ServerCommandTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveServerWorker implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $server;
    private $worker;
    private $forceRemove;

    public $tries = 1;
    public $timeout = 60;

    /**
     * InstallServerWorker constructor.
     *
     * @param Server  $server
     * @param Worker  $worker
     * @param Command $siteCommand
     * @param bool    $forceRemove
     */
    public function __construct(Server $server, Worker $worker, Command $siteCommand = null, $forceRemove = false)
    {
        $this->server = $server;
        $this->worker = $worker;
        $this->forceRemove = $forceRemove;
        $this->makeCommand($server, $worker, $siteCommand, 'Removing');
    }

    /**
     * @param \App\Services\Server\ServerService | ServerService $serverService
     *
     * @throws \Exception
     */
    public function handle(ServerService $serverService)
    {
        $sitesCount = $this->worker->sites->count();

        if ($this->forceRemove || ! $sitesCount) {
            $this->runOnServer(function () use ($serverService) {
                $serverService->getService(SystemService::WORKERS, $this->server)->removeWorker($this->worker);
            });

            if ($this->wasSuccessful()) {
                $this->server->cronJobs()->detach($this->worker->id);

                $this->worker->load('servers');
                if (0 == $this->worker->servers->count()) {
                    $this->worker->delete();
                }
            }
        } else {
            $this->updateServerCommand(0, 'Sites that are on this server using this worker', false);
        }
    }
}
