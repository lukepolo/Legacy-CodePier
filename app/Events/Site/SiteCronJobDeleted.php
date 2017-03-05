<?php

namespace App\Events\Site;

use App\Models\CronJob;
use App\Models\Site\Site;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Jobs\Server\CronJobs\RemoveServerCronJob;

class SiteCronJobDeleted
{
    use SerializesModels, ModelCommandTrait;

    /**
     * Create a new event instance.
     *
     * @param Site $site
     * @param CronJob $cronJob
     */
    public function __construct(Site $site, CronJob $cronJob)
    {
        $site->cronJobs()->detach($cronJob);

        if($site->provisionedServers->count()) {

            $siteCommand = $this->makeCommand($site, $cronJob);

            foreach ($cronJob->servers as $server) {
                dispatch(
                    (new RemoveServerCronJob($server, $cronJob,
                        $siteCommand))->onQueue(config('queue.channels.server_commands'))
                );
            }
        }
    }
}
