<?php

namespace App\Jobs\Server\Workers;

use App\Models\Command;
use App\Models\Server\Server;
use App\Models\Worker;
use Illuminate\Bus\Queueable;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Exceptions\ServerCommandFailed;
use App\Services\Systems\SystemService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Server\ServerServiceContract as ServerService;

class InstallServerWorker implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $server;
    private $worker;

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
        $this->makeCommand($server, $worker, $siteCommand);
    }

    /**
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @return \Illuminate\Http\JsonResponse
     * @throws ServerCommandFailed
     */
    public function handle(ServerService $serverService)
    {
        $this->runOnServer(function () use ($serverService) {
            $serverService->getService(SystemService::WORKERS, $this->server)->addWorker($this->worker);
        });

        if (! $this->wasSuccessful()) {
            if (\App::runningInConsole()) {
                throw new ServerCommandFailed($this->getCommandErrors());
            }
        } else {
            $this->server->workers()->save($this->worker);
        }

        return $this->remoteResponse();
    }
}
