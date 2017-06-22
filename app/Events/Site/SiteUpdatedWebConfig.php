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
        if ($site->provisionedServers->count()) {

            $siteCommand = $this->makeCommand($site, $site, 'Updating Web Config');

            foreach ($site->provisionedServers as $server) {
                $serverType = $server->type;
                if (
                    $serverType === SystemService::WEB_SERVER ||
                    $serverType === SystemService::LOAD_BALANCER ||
                    $serverType === SystemService::FULL_STACK_SERVER
                ) {
                    dispatch(
                        (new UpdateWebConfig($server, $site, $siteCommand))->onQueue(config('queue.channels.server_commands'))
                    );
                }
            }
        }
    }
}
