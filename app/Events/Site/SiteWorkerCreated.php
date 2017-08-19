<?php

namespace App\Events\Site;

use App\Models\Worker;
use App\Models\Site\Site;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Services\Systems\SystemService;
use App\Jobs\Server\Workers\InstallServerWorker;

class SiteWorkerCreated
{
    use SerializesModels, ModelCommandTrait;

    /**
     * Create a new event instance.
     *
     * @param Site $site
     * @param Worker $worker
     */
    public function __construct(Site $site, Worker $worker)
    {
        if ($site->hasWorkerServers()) {
            $availableServers = $site->filterServersByType([
                SystemService::WORKER_SERVER,
            ]);
        } else {
            $availableServers = $site->filterServersByType([
                SystemService::FULL_STACK_SERVER,
            ]);
        }

        if ($availableServers->count()) {
            $siteCommand = $this->makeCommand($site, $worker, 'Installing');

            foreach ($availableServers as $server) {
                rollback_dispatch(
                    (
                    new InstallServerWorker($server, $worker, $siteCommand)
                    )->onQueue(config('queue.channels.server_commands'))
                );
            }
        }
    }
}
