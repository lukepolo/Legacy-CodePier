<?php

namespace App\Events\Site;

use App\Models\Worker;
use App\Models\Site\Site;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
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
        if($site->provisionedServers->count()) {
            $siteCommand = $this->makeCommand($site, $worker);
            foreach ($site->provisionedServers as $server) {
                dispatch(new InstallServerWorker($server, $worker, $siteCommand));
            }
        }
    }
}
