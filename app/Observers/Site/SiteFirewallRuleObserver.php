<?php

namespace App\Observers\Site;

use App\Models\Server\ServerFirewallRule;
use App\Models\Site\SiteFirewallRule;
use App\Traits\ModelCommandTrait;

class SiteFirewallRuleObserver
{
    use ModelCommandTrait;

    /**
     * @param SiteFirewallRule $siteFirewallRule
     */
    public function created(SiteFirewallRule $siteFirewallRule)
    {
        foreach ($siteFirewallRule->site->provisionedServers as $server) {
            if (! ServerFirewallRule::where('port', $siteFirewallRule->port)
                ->where('from_ip', $siteFirewallRule->from_ip)
                ->where('description', $siteFirewallRule->description)
                ->count()
            ) {
                $serverFirewallRule = ServerFirewallRule::create([
                    'server_id' => $server->id,
                    'port' => $siteFirewallRule->port,
                    'from_ip' => $siteFirewallRule->from_ip,
                    'description' => $siteFirewallRule->description,
                    'site_firewall_rule_id' => $siteFirewallRule->id,
                ]);

                $serverFirewallRule->addHidden([
                    'command' => $this->makeCommand($siteFirewallRule),
                ]);

                $serverFirewallRule->save();
            }
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
