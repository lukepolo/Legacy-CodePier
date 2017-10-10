<?php

namespace App\Events\Server;

use App\Models\Worker;
use App\Models\Command;
use App\Models\Site\Site;
use App\Models\Server\Server;
use Illuminate\Queue\SerializesModels;
use App\Services\Systems\SystemService;
use App\Jobs\Server\Workers\RemoveServerWorker;
use App\Jobs\Server\Workers\InstallServerWorker;

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

        $this->site->workers->each(function (Worker $worker) {
            if ($this->site->hasWorkerServers()) {
                if ($this->serverType == SystemService::WORKER_SERVER) {
                    if (! $worker->hasServer($this->server)) {
                        $this->addServerWorker($worker);
                    }
                } else {
                    $this->removeServerWorker($worker);
                }
            } elseif (! $worker->hasServer($this->server) && $this->serverType == SystemService::FULL_STACK_SERVER) {
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
            (new RemoveServerWorker($this->server, $worker, $this->command))
                ->onQueue(config('queue.channels.server_commands'))
        );
    }

    private function removeServerWorker(Worker $worker)
    {
        dispatch(
            (new InstallServerWorker($this->server, $worker, $this->command))
                ->onQueue(config('queue.channels.server_commands'))
        );
    }
}
