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

class RemoveServerCronJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $server;
    private $cronJob;

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
        $sitesCount = $this->cronJob->sites->count();

        if(!$sitesCount) {
            $this->runOnServer(function () use ($serverService) {
                $serverService->removeCron($this->server, $this->cronJob);
            });

            if (! $this->wasSuccessful()) {
                throw new ServerCommandFailed($this->getCommandErrors());
            }
        }

        $this->server->cronJobs()->detach($this->cronJob->id);

        if(!$sitesCount) {
            $this->cronJob->load('servers');
            if ($this->cronJob->servers->count() == 0) {
                $this->cronJob->delete();
            }
        }
    }
}
