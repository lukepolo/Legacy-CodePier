<?php

namespace App\Events\Site;

use App\Models\CronJob;
use App\Models\Site\Site;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Jobs\Server\CronJobs\InstallServerCronJob;

class SiteCronJobCreated
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
        $siteCommand = $this->makeCommand($site, $cronJob);

        foreach ($cronJob->site->provisionedServers as $server) {
            if (! $server->cronJobs
                ->where('job', $cronJob->job)
                ->where('user', $cronJob->user)
                ->count()
            ) {
                dispatch(
                    (new InstallServerCronJob($server, $cronJob, $siteCommand))->onQueue(env('SERVER_COMMAND_QUEUE'))
                );
            }
        }
    }
}
