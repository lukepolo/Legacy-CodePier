<?php

namespace App\Events\Site;

use App\Jobs\Server\RestartWebServices;
use App\Models\Site\Site;
use Illuminate\Queue\SerializesModels;
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
            dispatch(
                (new RestartWebServices($server))->onQueue(config('queue.channels.server_commands'))
            );
        }
    }
}
