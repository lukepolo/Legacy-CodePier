<?php

namespace App\Events\Site;

use App\Jobs\Server\FirewallRules\RemoveServerFirewallRule;
use App\Models\FirewallRule;
use App\Models\Site\Site;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;

class SiteFirewallRuleDeleted
{
    use SerializesModels, ModelCommandTrait;

    /**
     * Create a new event instance.
     *
     * @param Site         $site
     * @param FirewallRule $firewallRule
     */
    public function __construct(Site $site, FirewallRule $firewallRule)
    {
        $site->firewallRules()->detach($firewallRule);

        $availableServers = $site->provisionedServers->filter(function ($server) use ($firewallRule) {
            return $server->ip !== $firewallRule->from_ip;
        });

        if ($availableServers->count()) {
            $siteCommand = $this->makeCommand($site, $firewallRule, 'Closing');

            foreach ($availableServers as $server) {
                dispatch(
                    (new RemoveServerFirewallRule($server, $firewallRule, $siteCommand))
                        ->onQueue(config('queue.channels.server_commands'))
                );
            }
        }
    }
}
