<?php

namespace App\Events\Server;

use App\Jobs\Server\Workers\InstallServerWorker;
use App\Jobs\Server\Workers\RemoveServerWorker;
use App\Models\Command;
use App\Models\Server\Server;
use App\Models\Site\Site;
use App\Models\Worker;
use App\Services\Systems\SystemService;
use Illuminate\Queue\SerializesModels;

class UpdateServerWorkers
{
    use SerializesModels;

    private $site;
    private $server;
    private $command;
    private $serverType;

    /**
     * Create a new event instance.
     *
     * @param Server  $server
     * @param Site    $site
     * @param Command $siteCommand
     */
    public function __construct(Server $server, Site $site, Command $siteCommand)
    {
        $this->site = $site;
        $this->server = $server;
        $this->command = $siteCommand;
        $this->serverType = $server->type;

        $this->site->workers->each(function (Worker $worker) {
            if ($this->site->hasWorkerServers()) {
                if (SystemService::WORKER_SERVER == $this->serverType) {
                    if (! $worker->hasServer($this->server)) {
                        $this->addServerWorker($worker);
                    }
                } else {
                    $this->removeServerWorker($worker);
                }
            } elseif (! $worker->hasServer($this->server) && SystemService::FULL_STACK_SERVER == $this->serverType) {
                $this->addServerWorker($worker);
            }
        });
    }

    /**
     * @param Worker $worker
     */
    private function addServerWorker(Worker $worker)
    {
        dispatch(
            (new InstallServerWorker($this->server, $worker, $this->command))
                ->onQueue(config('queue.channels.server_commands'))
        );
    }

    private function removeServerWorker(Worker $worker)
    {
        dispatch(
            (new RemoveServerWorker($this->server, $worker, $this->command, true))
                ->onQueue(config('queue.channels.server_commands'))
        );
    }
}
