<?php

namespace App\Events\Site;

use App\Models\Daemon;
use App\Models\Site\Site;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Services\Systems\SystemService;
use App\Jobs\Server\Daemons\InstallServerDaemon;

class SiteDaemonCreated
{
    use SerializesModels, ModelCommandTrait;

    /**
     * Create a new event instance.
     *
     * @param Site $site
     * @param Daemon $daemon
     */
    public function __construct(Site $site, Daemon $daemon)
    {
        $availableServers = $site->provisionedServers->filter(function ($server) use ($daemon) {
            $serverType = $server->type;

            if (! empty($daemon->server_types)) {
                return collect($daemon->server_types)->contains($serverType);
            } elseif (! empty($daemon->server_ids)) {
                return collect($daemon->server_ids)->contains($server->id);
            }

            if (
                $serverType === SystemService::WORKER_SERVER ||
                $serverType === SystemService::FULL_STACK_SERVER
            ) {
                return true;
            }

            return false;
        });

        if ($availableServers->count()) {
            $siteCommand = $this->makeCommand($site, $daemon, 'Installing');

            foreach ($availableServers as $server) {
                dispatch(
                    (new InstallServerDaemon($server, $daemon, $siteCommand))
                        ->onQueue(config('queue.channels.server_commands'))
                );
            }
        }
    }
}
