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
        $this->runOnServer(function () use ($serverService) {
            $serverService->removeCron($this->server, $this->cronJob);
        });

        if (! $this->wasSuccessful()) {
            if (\App::runningInConsole()) {
                throw new ServerCommandFailed($this->getCommandErrors());
            }
        } else {
            $this->server->cronJobs()->detach($this->cronJob->id);
        }

        $this->cronJob->load(['sites', 'servers']);

        if ($this->cronJob->sites->count() == 0 && $this->cronJob->servers->count() == 0) {
            $this->cronJob->delete();
        }

        return $this->remoteResponse();
    }
}
