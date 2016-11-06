<?php

namespace App\Jobs\Server\Workers;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Models\Server\ServerWorker;
use App\Services\Systems\SystemService;
use App\Traits\ServerCommandTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveServerWorker implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $serverWorker;

    /**
     * InstallServerWorker constructor.
     * @param ServerWorker $serverWorker
     */
    public function __construct(ServerWorker $serverWorker)
    {
        $this->serverWorker = $serverWorker;
    }

    /**
     * @param ServerService $serverService
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(ServerService $serverService)
    {
        $this->runOnServer(function () use ($serverService) {
            $serverService->getService(SystemService::WORKERS, $this->serverWorker->server)->removeWorker($this->serverWorker);
        });

        if ($this->wasSuccessful()) {
            $this->serverWorker->unsetEventDispatcher();
            $this->serverWorker->delete();
        }

        return $this->remoteResponse();
    }
}
