<?php

namespace App\Events;

use App\Models\Site\Site;
use App\Models\FirewallRule;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use App\Jobs\Server\FirewallRules\RemoveServerFirewallRule;

class SiteFirewallRuleDeleted
{
    use InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param Site $site
     * @param FirewallRule $firewallRule
     */
    public function __construct(Site $site, FirewallRule $firewallRule)
    {
        foreach ($site->provisionedServers as $server) {
            dispatch(
                (new RemoveServerFirewallRule($server, $firewallRule, $this->makeCommand($site, $firewallRule)))->onQueue(env('SERVER_COMMAND_QUEUE'))
            );
        }
    }
}
