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
        $availableServers = $site->provisionedServers->filter(function ($server) use ($cronJob) {
            if (! empty($cronJob->server_types)) {
                return collect($cronJob->server_types)->contains($server->type);
            } elseif (! empty($cronJob->server_ids)) {
                return collect($cronJob->server_ids)->contains($server->id);
            }

            return true;
        });

        if ($availableServers->count()) {
            $siteCommand = $this->makeCommand($site, $cronJob, 'Installing');

            foreach ($availableServers as $server) {
                dispatch(
                    (new InstallServerCronJob($server, $cronJob, $siteCommand))
                        ->onQueue(config('queue.channels.server_commands'))
                );
            }
        }
    }
}
