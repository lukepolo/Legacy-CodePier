<?php

namespace App\Observers\Site;

use App\Models\Site\SiteCronJob;
use App\Traits\ModelCommandTrait;
use App\Models\Server\ServerCronJob;

class SiteCronJobObserver
{
    use ModelCommandTrait;

    /**
     * @param SiteCronJob $siteCronJob
     */
    public function created(SiteCronJob $siteCronJob)
    {
        foreach ($siteCronJob->site->provisionedServers as $server) {
            if (! ServerCronJob::where('server_id', $server->id)
                ->where('job', $siteCronJob->job)
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
                    'command' => $this->makeCommand($siteCronJob),
                ]);

                $serverCronJob->save();
            } else {
                $siteCronJob->delete();
            }
        }
    }

    /**
     * @param SiteCronJob $siteCronJob
     */
    public function deleting(SiteCronJob $siteCronJob)
    {
        $siteCronJob->serverCronJobs->each(function ($serverCronJob) use($siteCronJob) {

            $serverCronJob->addHidden([
                'command' => $this->makeCommand($siteCronJob),
            ]);

            $serverCronJob->delete();
        });
    }
}
