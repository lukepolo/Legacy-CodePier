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
use Illuminate\Foundation\Bus\Dispatchable;
use App\Contracts\Server\ServerServiceContract as ServerService;

class RemoveServerCronJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $server;
    private $cronJob;

    public $tries = 1;
    public $timeout = 60;

    /**
     * RemoveServerCronJob constructor.
     * @param Server $server
     * @param CronJob $cronJob
     * @param Command $siteCommand
     * @internal param ServerCronJob $serverCronJob
     */
    public function __construct(Server $server, CronJob $cronJob, Command $siteCommand = null)
    {
        $this->server = $server;
        $this->cronJob = $cronJob;
        $this->makeCommand($server, $cronJob, $siteCommand, 'Removing');
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @throws \Exception
     */
    public function handle(ServerService $serverService)
    {
        $sitesCount = $this->cronJob->sites->count();

        if (! $sitesCount) {
            $this->runOnServer(function () use ($serverService) {
                $serverService->removeCron($this->server, $this->cronJob);
            });

            if ($this->wasSuccessful()) {
                $this->server->cronJobs()->detach($this->cronJob->id);

                $this->cronJob->load('servers');
                if ($this->cronJob->servers->count() == 0) {
                    $this->cronJob->delete();
                }
            }
        } else {
            $this->updateServerCommand(0, 'Sites that are on this server using this cron job', false);
        }
    }
}
