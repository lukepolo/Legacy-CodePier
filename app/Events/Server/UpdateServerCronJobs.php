<?php

namespace App\Events\Server;

use App\Models\Command;
use App\Models\CronJob;
use App\Models\Site\Site;
use App\Models\Server\Server;
use Illuminate\Queue\SerializesModels;
use App\Services\Systems\SystemService;
use App\Jobs\Server\CronJobs\RemoveServerCronJob;
use App\Jobs\Server\CronJobs\InstallServerCronJob;

class UpdateServerCronJobs
{
    use SerializesModels;

    private $site;
    private $server;
    private $command;
    private $serverType;

    /**
     * Create a new event instance.
     *
     * @param Server $server
     * @param Site $site
     * @param Command $siteCommand
     */
    public function __construct(Server $server, Site $site, Command $siteCommand)
    {
        $this->site = $site;
        $this->server = $server;
        $this->command = $siteCommand;
        $this->serverType = $server->type;

        // TODO - cronjobs have the ability to select what server types they are installed on
        $this->site->cronJobs->each(function (CronJob $cronJob) {

            if($this->site->hasWorkerServers()) {
                if($this->serverType == SystemService::WORKER_SERVER) {
                    if(!$cronJob->hasServer($this->server)) {
                        $this->installCronJob($cronJob);
                    }
                } else {
                    $this->removeCronJob($cronJob);
                }
            } else if(!$cronJob->hasServer($this->server)) {
                $this->installCronJob($cronJob);
            }
        });
    }

    /**
     * @param CronJob $cronJob
     */
    private function installCronJob(CronJob $cronJob)
    {
        dispatch(
            (new InstallServerCronJob($this->server, $cronJob, $this->command))->onQueue(config('queue.channels.server_commands'))
        );
    }

    private function removeCronJob(CronJob $cronJob)
    {
        dispatch(
            (new RemoveServerCronJob($this->server, $cronJob, $this->command))->onQueue(config('queue.channels.server_commands'))
        );
    }
}
