<?php

namespace App\Events\Site;

use App\Models\Site\Site;
use App\Jobs\Server\RestartDatabases;
use App\Services\Systems\SystemService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class SiteRestartDatabases
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
                $serverType === SystemService::DATABASE_SERVER ||
                $serverType === SystemService::FULL_STACK_SERVER
            ) {
                dispatch(
                    (new RestartDatabases($server))->onQueue(config('queue.channels.server_commands'))
                );
            }
        }
    }
}
