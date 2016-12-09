<?php

namespace App\Observers\Site;

use App\Traits\ModelCommandTrait;
use App\Models\Site\SiteFirewallRule;
use App\Models\Server\ServerFirewallRule;

class SiteFirewallRuleObserver
{
    use ModelCommandTrait;

    /**
     * @param SiteFirewallRule $siteFirewallRule
     */
    public function created(SiteFirewallRule $siteFirewallRule)
    {
        foreach ($siteFirewallRule->site->provisionedServers as $server) {
            if (! ServerFirewallRule::where('server_id', $server->id)
                ->where('port', $siteFirewallRule->port)
                ->where('from_ip', $siteFirewallRule->from_ip)
                ->count()
            ) {
                $serverFirewallRule = new ServerFirewallRule([
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
            } else {
                $siteFirewallRule->delete();
            }
        }
    }

    /**
     * @param SiteFirewallRule $siteFirewallRule
     */
    public function deleting(SiteFirewallRule $siteFirewallRule)
    {
        $siteFirewallRule->serverFirewallRules->each(function ($serverFirewallRule) use($siteFirewallRule) {

            $serverFirewallRule->addHidden([
                'command' => $this->makeCommand($siteFirewallRule),
            ]);

            $serverFirewallRule->delete();
        });
    }
}
