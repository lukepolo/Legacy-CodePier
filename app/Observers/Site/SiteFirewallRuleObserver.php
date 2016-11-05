<?php

namespace App\Observers\Site;

use App\Models\Server\ServerFirewallRule;
use App\Models\Site\SiteFirewallRule;

class SiteFirewallRuleObserver
{
    /**
     * @param SiteFirewallRule $siteFirewallRule
     */
    public function created(SiteFirewallRule $siteFirewallRule)
    {
        foreach ($siteFirewallRule->site->provisionedServers as $server) {
            ServerFirewallRule::create([
                'server_id' => $server->id,
                'port' => $siteFirewallRule->port,
                'from_ip' => $siteFirewallRule->from_ip,
                'description' => $siteFirewallRule->description,
                'site_firewall_rule_id' => $siteFirewallRule->id,
            ]);
        }
    }

    /**
     * @param SiteFirewallRule $siteFirewallRule
     */
    public function deleting(SiteFirewallRule $siteFirewallRule)
    {
        $siteFirewallRule->serverFirewallRules->each(function ($serverFirewallRule) {
            $serverFirewallRule->delete();
        });
    }
}
