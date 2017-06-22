<?php

namespace App\Events\Site;

use App\Models\Site\Site;
use App\Traits\ModelCommandTrait;
use App\Jobs\Site\RenameSiteDomain;
use Illuminate\Queue\SerializesModels;
use App\Services\Systems\SystemService;

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
            $siteCommand = $this->makeCommand($site, $site, 'Renaming site '.$oldDomain.'to'.$newDomain);

            foreach ($site->provisionedServers as $server) {
                $serverType = $server->type;
                if (
                    $serverType === SystemService::WEB_SERVER ||
                    $serverType === SystemService::LOAD_BALANCER ||
                    $serverType === SystemService::FULL_STACK_SERVER
                ) {
                    dispatch(
                        (new RenameSiteDomain($server, $site, $newDomain, $oldDomain,
                            $siteCommand))->onQueue(config('queue.channels.server_commands'))
                    );
                }
            }
        }
    }
}
