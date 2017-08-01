<?php

namespace App\Events\Site;

use App\Models\Site\Site;
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
        foreach ($site->servers as $server) {
            $siteCommand = $this->makeCommand($site, $server, 'Updating Server '.$server->name.' for '.$site->name);
            event(new UpdateServerConfigurations($server, $site, $siteCommand));
        }
    }
}
