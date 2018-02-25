<?php

namespace App\Jobs\Server\CronJobs;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Models\Command;
use App\Models\CronJob;
use App\Models\Server\Server;
use App\Traits\ServerCommandTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class InstallServerCronJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $server;
    private $cronJob;

    public $tries = 1;
    public $timeout = 60;

    /**
     * Create a new job instance.
     *
     * @param Server  $server
     * @param CronJob $cronJob
     * @param Command $siteCommand
     */
    public function __construct(Server $server, CronJob $cronJob, Command $siteCommand = null)
    {
        $this->server = $server;
        $this->cronJob = $cronJob;
        $this->makeCommand($server, $cronJob, $siteCommand, 'Installing');
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     *
     * @throws \Exception
     */
    public function handle(ServerService $serverService)
    {
        if (
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
        }
    }
}
