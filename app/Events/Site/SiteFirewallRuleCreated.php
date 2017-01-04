<?php

namespace App\Events;

use App\Jobs\Server\FirewallRules\InstallServerFirewallRule;
use App\Models\FirewallRule;
use App\Models\Site\Site;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class SiteFirewallRuleCreated
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
            if (!$server->firewallRules
                ->where('port', $firewallRule->port)
                ->where('from_ip', $firewallRule->from_ip)
                ->count()
            ) {
                dispatch(
                    (new InstallServerFirewallRule($server, $firewallRule, $this->makeCommand($site, $firewallRule)))->onQueue(env('SERVER_COMMAND_QUEUE'))
                );
            }
        }
    }
}
