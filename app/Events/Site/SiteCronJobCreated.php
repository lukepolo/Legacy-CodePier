<?php

namespace App\Events\Site;

use App\Models\CronJob;
use App\Models\Site\Site;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use App\Jobs\Server\CronJobs\InstallServerCronJob;

class SiteCronJobCreated
{
    use InteractsWithSockets, SerializesModels, ModelCommandTrait;

    /**
     * Create a new event instance.
     *
     * @param Site $site
     * @param CronJob $cronJob
     */
    public function __construct(Site $site, CronJob $cronJob)
    {
        foreach ($cronJob->site->provisionedServers as $server) {
            if (! $server->cronJobs
                ->where('job', $cronJob->job)
                ->where('user', $cronJob->user)
                ->count()
            ) {
                dispatch(
                    (new InstallServerCronJob($server, $cronJob, $this->makeCommand($site, $cronJob)))->onQueue(env('SERVER_COMMAND_QUEUE'))
                );
            }
        }
    }
}
