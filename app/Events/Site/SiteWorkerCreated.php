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
        if ($site->provisionedServers->count()) {
            $siteCommand = $this->makeCommand($site, $worker, 'Installing');

            foreach ($site->provisionedServers as $server) {

                $serverType = $server->type;

                $workerServerExists = $site->servers->first(function ($server) {
                    return $server->type === SystemService::WORKER_SERVER;
                });

                if (
                    $serverType === SystemService::WORKER_SERVER ||
                    (
                        empty($workerServerExists) &&
                        $serverType === SystemService::FULL_STACK_SERVER
                    )

                ) {
                    dispatch(
                        (
                            new InstallServerWorker($server, $worker, $siteCommand)
                        )->onQueue(config('queue.channels.server_commands'))
                    );
                }
            }
        }
    }
}
