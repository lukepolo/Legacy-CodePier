<?php

namespace App\Events\Site;

use App\Models\Site\Site;
use App\Models\FirewallRule;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Jobs\Server\FirewallRules\InstallServerFirewallRule;

class SiteFirewallRuleCreated
{
    use SerializesModels, ModelCommandTrait;

    /**
     * Create a new event instance.
     *
     * @param Site $site
     * @param FirewallRule $firewallRule
     */
    public function __construct(Site $site, FirewallRule $firewallRule)
    {
        if($site->provisionedServers->count()) {
            $siteCommand = $this->makeCommand($site, $firewallRule);

            foreach ($site->provisionedServers as $server) {
                dispatch(
                    (new InstallServerFirewallRule($server, $firewallRule,
                        $siteCommand))->onQueue(config('queue.channels.server_commands'))
                );
            }
        }
    }
}
