<?php

namespace App\Observers\Site;

use App\Models\Server\ServerFirewallRule;
use App\Models\Site\SiteFirewallRule;

/**
 * Class SiteFirewallRuleObserver.
 */
class SiteFirewallRuleObserver
{
    public function created(SiteFirewallRule $siteFirewallRule)
    {
        foreach($siteFirewallRule->site->servers as $server) {
            ServerFirewallRule::create([
                'server_id' => $server->id,
                'port' => $siteFirewallRule->port,
                'from_ip' => $siteFirewallRule->from_ip,
                'description' => $siteFirewallRule->description,
                'site_firewall_rule_id' => $siteFirewallRule->id
            ]);
        }
    }

    public function deleting(SiteFirewallRule $siteFirewallRule)
    {
        $siteFirewallRule->serverFirewallRules->each(function($serverFirewallRule) {
            $serverFirewallRule->delete();
        });
    }
}
