<?php

namespace App\Events\Site;

use App\Jobs\Server\RestartDatabases;
use App\Models\Site\Site;
use App\Services\Systems\SystemService;
use App\Traits\ModelCommandTrait;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SiteRestartDatabases
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
            SystemService::DATABASE_SERVER,
            SystemService::FULL_STACK_SERVER,
        ]);

        if ($availableServers->count()) {
            foreach ($availableServers as $server) {
                $siteCommand = $this->makeCommand($site, $server, 'Restarting Databases');
                dispatch(
                    (new RestartDatabases($server, $siteCommand))
                        ->onQueue(config('queue.channels.server_commands'))
                );
            }
        }
    }
}
