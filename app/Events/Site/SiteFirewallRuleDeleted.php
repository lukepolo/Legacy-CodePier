<?php

namespace App\Events;

use App\Models\Site\Site;
use App\Models\FirewallRule;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Jobs\Server\FirewallRules\RemoveServerFirewallRule;

class SiteFirewallRuleDeleted
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
        $siteCommand = $this->makeCommand($site, $firewallRule);

        foreach ($site->provisionedServers as $server) {
            dispatch(
                (new RemoveServerFirewallRule($server, $firewallRule, $siteCommand))->onQueue(env('SERVER_COMMAND_QUEUE'))
            );
        }
    }
}
