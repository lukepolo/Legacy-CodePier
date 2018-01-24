<?php

namespace App\Events\Site;

use App\Models\Site\Site;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Events\Server\UpdateServerConfigurations;

class FixSiteServerConfigurations
{
    use SerializesModels, ModelCommandTrait;

    public $site;

    /**
     * Create a new job instance.
     *
     * @param Site $site
     */
    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    public function handle() {
        $serverProvisioning = $this->site->servers->first(function ($server) {
            return $server->progress < 100;
        });

        if (empty($serverProvisioning)) {
            foreach ($this->site->provisionedServers as $server) {
                if (empty($excludeServer) || $server->id != $excludeServer->id) {
                    $siteCommand = $this->makeCommand($this->site, $server, 'Updating Server '.$server->name.' for '.$this->site->name);
                    event(new UpdateServerConfigurations($server, $this->site, $siteCommand));
                }
            }
        }
    }
}
