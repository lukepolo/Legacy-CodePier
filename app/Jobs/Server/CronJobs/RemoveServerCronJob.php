<?php

namespace App\Jobs\Server\CronJobs;

use App\Models\Command;
use App\Models\CronJob;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Contracts\Server\ServerServiceContract as ServerService;

class RemoveServerCronJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $server;
    private $cronJob;
    private $forceRemove;

    public $tries = 1;
    public $timeout = 60;

    /**
     * RemoveServerCronJob constructor.
     * @param Server $server
     * @param CronJob $cronJob
     * @param Command $siteCommand
     * @param bool $forceRemove
     * @internal param ServerCronJob $serverCronJob
     */
    public function __construct(Server $server, CronJob $cronJob, Command $siteCommand = null, $forceRemove = false)
    {
        $this->server = $server;
        $this->cronJob = $cronJob;
        $this->forceRemove = $forceRemove;
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

        if ($this->forceRemove || ! $this->daemon->installableOnServer($this->server) || ! $sitesCount) {
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
            // TODO -This isn't exactly true, mabye these cronjobs dont belong on this server , so we need to check
            $this->updateServerCommand(0, 'Sites that are on this server using this cron job', false);
        }
    }
}
