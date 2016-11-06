<?php

namespace App\Jobs\Server\CronJobs;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Models\Server\ServerCronJob;
use App\Traits\ServerCommandTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class InstallServerCronJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

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
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(ServerService $serverService)
    {
        $this->runOnServer(function () use ($serverService) {
            $serverService->installCron($this->serverCronJob);
        });

        if (! $this->wasSuccessful()) {
            $this->serverCronJob->unsetEventDispatcher();
            $this->serverCronJob->delete();
        }

        return $this->remoteResponse();
    }
}
