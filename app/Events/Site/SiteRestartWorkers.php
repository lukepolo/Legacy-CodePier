<?php

namespace App\Events\Site;

use App\Models\Site\Site;
use App\Jobs\Server\RestartWorkers;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Services\Systems\SystemService;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class SiteRestartWorkers
{
    use Dispatchable, InteractsWithSockets, SerializesModels, ModelCommandTrait;

    /**
     * Create a new event instance.
     *
     * @param Site $site
     */
    public function __construct(Site $site)
    {
        foreach ($site->provisionedServers as $server) {
            $serverType = $server->type;

            if (
                $serverType === SystemService::WORKER_SERVER ||
                $serverType === SystemService::FULL_STACK_SERVER
            ) {

                $siteCommand = $this->makeCommand($site, $server, 'Restarting Workers');
                dispatch(
                    (new RestartWorkers($server, $siteCommand))->onQueue(config('queue.channels.server_commands'))
                );
            }
        }
    }
}
