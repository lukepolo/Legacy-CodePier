<?php

namespace App\Events\Site;

use App\Models\Site\Site;
use App\Traits\ModelCommandTrait;
use App\Jobs\Site\UpdateWebConfig;
use Illuminate\Queue\SerializesModels;
use App\Services\Systems\SystemService;

class SiteUpdatedWebConfig
{
    use SerializesModels, ModelCommandTrait;

    /**
     * Create a new event instance.
     *
     * @param Site $site
     */
    public function __construct(Site $site)
    {
        $availableServers = $site->filterServersByType([
            SystemService::WEB_SERVER,
            SystemService::LOAD_BALANCER,
            SystemService::FULL_STACK_SERVER,
        ]);

        if ($availableServers->count()) {
            $siteCommand = $this->makeCommand($site, $site, 'Updating Web Config');

            foreach ($availableServers as $server) {
                rollback_dispatch(
                    (new UpdateWebConfig($server, $site, $siteCommand))->onQueue(config('queue.channels.server_commands'))
                );
            }
        }
    }
}
