<?php

namespace App\Events\Site;

use App\Models\Site\Site;
use App\Models\Server\Server;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Events\Server\UpdateServerConfigurations;

class FixSiteServerConfigurations
{
    use SerializesModels, ModelCommandTrait;

    /**
     * Create a new job instance.
     *
     * @param Site $site
     */
    public function __construct(Site $site)
    {
        $serverProvisioning = $site->servers->first(function($server) {
            return $server->progress < 100;
        });

        if(empty($serverProvisioning)) {
            foreach ($site->provisionedServers as $server) {
                if (empty($excludeServer) || $server->id != $excludeServer->id) {
                    $siteCommand = $this->makeCommand($site, $server, 'Updating Server '.$server->name.' for '.$site->name);
                    event(new UpdateServerConfigurations($server, $site, $siteCommand));
                }
            }
        }
    }
}
