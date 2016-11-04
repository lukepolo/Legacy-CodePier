<?php

namespace App\Jobs\Server;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Models\Server\ServerWorker;
use App\Services\Systems\SystemService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class InstallServerWorker implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

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
     * @return mixed
     */
    public function handle(ServerService $serverService)
    {
        return $this->runOnServer(function () use ($serverService) {
            $serverService->getService(SystemService::WORKERS, $this->serverWorker->server)->addWorker($this->serverWorker);
        });
    }
}
