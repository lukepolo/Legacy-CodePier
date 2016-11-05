<?php

namespace App\Observers\Site;

use App\Models\Server\ServerCronJob;
use App\Models\Site\SiteCronJob;

class SiteCronJobObserver
{
    /**
     * @param SiteCronJob $siteCronJob
     */
    public function created(SiteCronJob $siteCronJob)
    {
        foreach ($siteCronJob->site->provisionedServers as $server) {
            ServerCronJob::create([
                'server_id' => $server->id,
                'job' => $siteCronJob->job,
                'user' => $siteCronJob->user,
                'site_cron_job_id' => $siteCronJob->id,
            ]);
        }
    }

    /**
     * @param SiteCronJob $siteCronJob
     */
    public function deleting(SiteCronJob $siteCronJob)
    {
        $siteCronJob->serverCronJobs->each(function ($serverCronJob) {
            $serverCronJob->delete();
        });
    }
}
