<?php

namespace App\Events\Server;

use App\Models\Daemon;
use App\Models\Command;
use App\Models\Site\Site;
use App\Models\Server\Server;
use App\Services\Systems\SystemService;
use Illuminate\Queue\SerializesModels;
use App\Jobs\Server\Daemons\RemoveServerDaemon;
use App\Jobs\Server\Daemons\InstallServerDaemon;

class UpdateServerDaemons
{
    use SerializesModels;

    private $site;
    private $server;
    private $command;
    private $serverType;

    /**
     * Create a new event instance.
     *
     * @param Server $server
     * @param Site $site
     * @param Command $siteCommand
     */
    public function __construct(Server $server, Site $site, Command $siteCommand)
    {
        $this->site = $site;
        $this->server = $server;
        $this->command = $siteCommand;
        $this->serverType = $server->type;

        $this->site->daemons->each(function (Daemon $daemon) {
            if ($this->site->hasWorkerServers()) {
                if ($this->serverType == SystemService::WORKER_SERVER) {
                    if (! $daemon->hasServer($this->server)) {
                        $this->installDaemon($daemon);
                    }
                } else {
                    $this->removeDaemon($daemon);
                }
            } elseif (! $daemon->hasServer($this->server) && $this->serverType == SystemService::FULL_STACK_SERVER) {
                $this->installDaemon($daemon);
            }
        });
    }

    /**
     * @param Daemon $daemon
     */
    private function installDaemon(Daemon $daemon)
    {
        dispatch(
            (new InstallServerDaemon($this->server, $daemon, $this->command))
                ->onQueue(config('queue.channels.server_commands'))
        );
    }

    /**
     * @param Daemon $daemon
     */
    private function removeDaemon(Daemon $daemon)
    {
        dispatch(
            (new RemoveServerDaemon($this->server, $daemon, $this->command, true))
                ->onQueue(config('queue.channels.server_commands'))
        );
    }
}
