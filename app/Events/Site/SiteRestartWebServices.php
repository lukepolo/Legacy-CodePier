<?php

namespace App\Events\Site;

use App\Models\Site\Site;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Jobs\Server\RestartWebServices;
use App\Services\Systems\SystemService;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class SiteRestartWebServices
{
    use Dispatchable, InteractsWithSockets, SerializesModels, ModelCommandTrait;

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
            foreach ($availableServers as $server) {
                $siteCommand = $this->makeCommand($site, $server, 'Restarting Web Services');

                dispatch(
                    (new RestartWebServices($server, $siteCommand))->onQueue(config('queue.channels.server_commands'))
                );
            }
        }
    }
}
