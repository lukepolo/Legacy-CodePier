<?php

namespace App\Events\Site;

use App\Models\Worker;
use App\Models\Site\Site;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Services\Systems\SystemService;
use App\Jobs\Server\Workers\RemoveServerWorker;

class SiteWorkerDeleted
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
        $site->workers()->detach($worker);

        if ($site->provisionedServers->count()) {

            $siteCommand = $this->makeCommand($site, $worker, 'Removing');

            foreach ($site->provisionedServers as $server) {
                $serverType = $server->type;

                if (
                    $serverType === SystemService::WORKER_SERVER ||
                    $serverType === SystemService::FULL_STACK_SERVER
                ) {
                    dispatch(
                        (
                        new RemoveServerWorker($server, $worker, $siteCommand)
                        )->onQueue(config('queue.channels.server_commands'))
                    );
                }
            }
        }
    }
}
