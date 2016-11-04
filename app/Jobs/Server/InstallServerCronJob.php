<?php

namespace App\Jobs\Server;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Exceptions\Traits\ServerErrorTrait;
use App\Models\Server\ServerCronJob;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class InstallServerCronJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerErrorTrait;

    private $serverCronJob;

    /**
     * Create a new job instance.
     * @param ServerCronJob $serverCronJob
     */
    public function __construct(ServerCronJob $serverCronJob)
    {
        $this->serverCronJob = $serverCronJob;
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     *
     * @return \App\Classes\FailedRemoteResponse|\App\Classes\SuccessRemoteResponse
     */
    public function handle(ServerService $serverService)
    {
        $this->runOnServer(function () use ($serverService) {
            $serverService->installCron($this->serverCronJob);
        });

        if(!$this->wasSuccessful()) {
            $this->serverCronJob->delete();
        }

        return $this->remoteResponse();
    }
}
