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

class InstallServerWorker implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $server;
    private $worker;

    public $tries = 3;
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
        $this->makeCommand($server, $worker, $siteCommand, 'Installing');
    }

    /**
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @throws \Exception
     */
    public function handle(ServerService $serverService)
    {
        if ($this->server->workers->keyBy('id')->get($this->worker->id)) {
            $this->updateServerCommand(0, 'Sever already has worker installed');
        } else {
            $this->runOnServer(function () use ($serverService) {
                $serverService->getService(SystemService::WORKERS, $this->server)->addWorker($this->worker);
            });

            if ($this->wasSuccessful()) {
                $this->server->workers()->save($this->worker);
            }
        }
    }
}
