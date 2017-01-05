<?php

namespace App\Events\Site;

use App\Jobs\Server\Workers\InstallServerWorker;
use App\Models\Site\Site;
use App\Models\Worker;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;

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
        $siteCommand = $this->makeCommand($site, $worker);
        foreach ($site->provisionedServers as $server) {
            dispatch(new InstallServerWorker($server, $worker, $siteCommand));
        }
    }
}
