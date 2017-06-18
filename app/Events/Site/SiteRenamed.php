<?php

namespace App\Events\Site;

use App\Models\Site\Site;
use App\Traits\ModelCommandTrait;
use App\Jobs\Site\RenameSiteDomain;
use Illuminate\Queue\SerializesModels;

class SiteRenamed
{
    use SerializesModels, ModelCommandTrait;

    /**
     * Create a new event instance.
     *
     * @param Site $site
     * @param $newDomain
     * @param $oldDomain
     */
    public function __construct(Site $site, $newDomain, $oldDomain)
    {
        if ($site->provisionedServers->count()) {

            $siteCommand = $this->makeCommand($site, $site, 'Renaming site '.$oldDomain. 'to' . $newDomain);

            foreach ($site->provisionedServers as $server) {
                dispatch(
                    (new RenameSiteDomain($server, $site, $newDomain, $oldDomain, $siteCommand))->onQueue(config('queue.channels.server_commands'))
                );
            }
        }
    }
}
