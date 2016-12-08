<?php

namespace App\Jobs\Server\Workers;

use App\Exceptions\ServerCommandFailed;
use Illuminate\Bus\Queueable;
use App\Traits\ServerCommandTrait;
use App\Models\Server\ServerWorker;
use Illuminate\Queue\SerializesModels;
use App\Services\Systems\SystemService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Server\ServerServiceContract as ServerService;

class InstallServerWorker implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $serverWorker;

    /**
     * InstallServerWorker constructor.
     * @param ServerWorker $serverWorker
     */
    public function __construct(ServerWorker $serverWorker)
    {
        $this->makeCommand($serverWorker);
        $this->serverWorker = $serverWorker;
    }

    /**
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @return \Illuminate\Http\JsonResponse
     * @throws ServerCommandFailed
     */
    public function handle(ServerService $serverService)
    {
        $this->runOnServer(function () use ($serverService) {
            $serverService->getService(SystemService::WORKERS, $this->serverWorker->server)->addWorker($this->serverWorker);
        });

        if (! $this->wasSuccessful()) {
            $this->serverWorker->unsetEventDispatcher();
            $this->serverWorker->delete();
            if(\App::runningInConsole()) {
                throw new ServerCommandFailed($this->getCommandErrors());
            }
        }

        return $this->remoteResponse();
    }
}
