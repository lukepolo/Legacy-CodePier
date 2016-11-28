<?php

namespace App\Observers\Site;

use App\Models\Server\ServerCronJob;
use App\Models\Site\SiteCronJob;
use App\Traits\ModelCommandTrait;

class SiteCronJobObserver
{
    use ModelCommandTrait;

    /**
     * @param SiteCronJob $siteCronJob
     */
    public function created(SiteCronJob $siteCronJob)
    {
        foreach ($siteCronJob->site->provisionedServers as $server) {
            if (! ServerCronJob::where('job', $siteCronJob->job)
                ->where('user', $siteCronJob->user)
                ->count()
            ) {
                $serverCronJob = new ServerCronJob([
                    'server_id' => $server->id,
                    'job' => $siteCronJob->job,
                    'user' => $siteCronJob->user,
                    'site_cron_job_id' => $siteCronJob->id,
                ]);

                $serverCronJob->addHidden([
                    'command' => $this->makeCommand($serverCronJob, $siteCronJob->site_id)
                ]);

                $serverCronJob->save();
            }
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
