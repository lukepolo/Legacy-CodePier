<?php

namespace App\Jobs\Server\CronJobs;

use App\Models\Command;
use App\Models\CronJob;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Exceptions\ServerCommandFailed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Server\ServerServiceContract as ServerService;

class InstallServerCronJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $server;
    private $cronJob;

    /**
     * Create a new job instance.
     * @param Server $server
     * @param CronJob $cronJob
     * @param Command $siteCommand
     */
    public function __construct(Server $server, CronJob $cronJob, Command $siteCommand = null)
    {
        $this->server = $server;
        $this->cronJob = $cronJob;
        $this->makeCommand($server, $cronJob, $siteCommand);
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @return \Illuminate\Http\JsonResponse
     * @throws ServerCommandFailed
     */
    public function handle(ServerService $serverService)
    {
        if(
            $this->server->cronJobs
            ->where('job', $this->cronJob->job)
            ->where('user', $this->cronJob->user)
            ->count()
            ||
            $this->server->cronJobs->keyBy('id')->get($this->cronJob->id)
        ) {
            $this->updateServerCommand(0, 'Sever already has cron job : '.$this->cronJob->job);
        } else {
            $this->runOnServer(function () use ($serverService) {
                $serverService->installCron($this->server, $this->cronJob);
            });

            if ($this->wasSuccessful()) {
                $this->server->cronJobs()->save($this->cronJob);
            }

            throw new ServerCommandFailed($this->getCommandErrors());
        }
    }
}
