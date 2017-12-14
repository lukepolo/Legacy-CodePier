<?php

namespace App\Jobs\Server\Workers;

use App\Models\Worker;
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

class RemoveServerWorker implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $server;
    private $worker;

    public $tries = 1;
    public $timeout = 60;

    /**
     * InstallServerWorker constructor.
     * @param Server $server
     * @param Worker $worker
     * @param Command $siteCommand
     */
    public function __construct(Server $server, Worker $worker, Command $siteCommand = null)
    {
        $this->server = $server;
        $this->worker = $worker;
        $this->makeCommand($server, $worker, $siteCommand, 'Removing');
    }

    /**
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @throws \Exception
     */
    public function handle(ServerService $serverService)
    {
        $sitesCount = $this->worker->sites->count();

        if (! $sitesCount) {
            $this->runOnServer(function () use ($serverService) {
                $serverService->getService(SystemService::WORKERS, $this->server)->removeWorker($this->worker);
            });

            if ($this->wasSuccessful()) {
                $this->server->cronJobs()->detach($this->worker->id);

                $this->worker->load('servers');
                if ($this->worker->servers->count() == 0) {
                    $this->worker->delete();
                }
            }
        } else {
            $this->updateServerCommand(0, 'Sites that are on this server using this worker', false);
        }
    }
}
