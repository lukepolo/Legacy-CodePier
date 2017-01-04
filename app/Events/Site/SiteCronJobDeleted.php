<?php

namespace App\Events\Site;

use App\Jobs\Server\CronJobs\RemoveServerCronJob;
use App\Models\CronJob;
use App\Models\Site\Site;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class SiteCronJobDeleted
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
        foreach ($site->provisionedServers as $server) {
            dispatch(
                (new RemoveServerCronJob($server, $cronJob, $this->makeCommand($site, $cronJob)))->onQueue(env('SERVER_COMMAND_QUEUE'))
            );
        }
    }
}
