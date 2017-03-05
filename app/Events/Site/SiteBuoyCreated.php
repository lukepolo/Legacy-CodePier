<?php

namespace App\Events\Site;

use App\Models\Buoy;
use App\Models\Site\Site;
use App\Traits\ModelCommandTrait;
use App\Jobs\Server\Buoys\InstallBuoy;
use Illuminate\Queue\SerializesModels;

class SiteBuoyCreated
{
    use SerializesModels, ModelCommandTrait;

    /**
     * Create a new event instance.
     *
     * @param Site $site
     * @param Buoy $buoy
     */
    public function __construct(Site $site, Buoy $buoy)
    {
        if($site->provisionedServers->count()) {

            $siteCommand = $this->makeCommand($site, $buoy);

            foreach ($site->provisionedServers as $server) {
                dispatch(
                    (new InstallBuoy($server, $buoy, $siteCommand))->onQueue(config('queue.channels.server_commands'))
                );
            }

        }
    }
}
