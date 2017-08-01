<?php

namespace App\Events\Site;

use App\Models\CronJob;
use App\Models\Site\Site;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Services\Systems\SystemService;
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
        $availableServers = $site->filterServersByType([
            SystemService::WEB_SERVER,
            SystemService::FULL_STACK_SERVER
        ]);

        if($availableServers->count()) {

            $siteCommand = $this->makeCommand($site, $cronJob, 'Installing');

            foreach ($availableServers as $server) {
                dispatch(
                    (new InstallServerCronJob($server, $cronJob, $siteCommand))->onQueue(config('queue.channels.server_commands'))
                );
            }

        }
    }
}
