<?php

namespace App\Events\Site;

use App\Jobs\Server\RestartServer;
use App\Models\Site\Site;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SiteRestartServers
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
            dispatch(
                (new RestartServer($server))
                    ->onQueue(config('queue.channels.server_commands'))
            );
        }
    }
}
