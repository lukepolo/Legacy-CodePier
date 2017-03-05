<?php

namespace App\Events\Site;

use App\Models\Buoy;
use App\Models\Site\Site;
use App\Traits\ModelCommandTrait;
use App\Jobs\Server\Buoys\RemoveBuoy;
use Illuminate\Queue\SerializesModels;

class SiteBuoyDeleted
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
        $site->buoys()->detach($buoy);

        if($site->provisionedServers->count()) {

            $siteCommand = $this->makeCommand($site, $buoy);

            foreach ($site->provisionedServers as $server) {
                dispatch(
                    (new RemoveBuoy($server, $buoy, $siteCommand))->onQueue(config('queue.channels.server_commands'))
                );
            }
        }
    }
}
