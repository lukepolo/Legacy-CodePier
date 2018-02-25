<?php

namespace App\Events\Site;

use App\Models\Daemon;
use App\Models\Site\Site;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Jobs\Server\Daemons\RemoveServerDaemon;

class SiteDaemonDeleted
{
    use SerializesModels, ModelCommandTrait;

    /**
     * Create a new event instance.
     *
     * @param Site   $site
     * @param Daemon $daemon
     */
    public function __construct(Site $site, Daemon $daemon)
    {
        $site->daemons()->detach($daemon);

        if ($daemon->servers()->count()) {
            $siteCommand = $this->makeCommand($site, $daemon, 'Removing');

            foreach ($daemon->servers as $server) {
                dispatch(
                    (new RemoveServerDaemon($server, $daemon, $siteCommand))
                        ->onQueue(config('queue.channels.server_commands'))
                );
            }
        }
    }
}
