<?php

namespace App\Events\Site;

use App\Models\Site\Site;
use App\Services\Systems\SystemService;
use Illuminate\Queue\SerializesModels;
use App\Jobs\Server\RestartWebServices;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class SiteRestartWebServices
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param Site $site
     */
    public function __construct(Site $site)
    {
        foreach ($site->provisionedServers as $server) {

            $serverType = $server->type;

            if(
                $serverType === SystemService::WEB_SERVER ||
                $serverType === SystemService::LOAD_BALANCER ||
                $serverType === SystemService::FULL_STACK_SERVER
            ) {
                dispatch(
                    (new RestartWebServices($server))->onQueue(config('queue.channels.server_commands'))
                );
            }

        }
    }
}
