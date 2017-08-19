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
        $availableServers = $site->provisionedServers->filter(function ($server) use ($firewallRule) {
            return $server->ip !== $firewallRule->from_ip;
        });

        if ($availableServers->count()) {
            $siteCommand = $this->makeCommand($site, $firewallRule, 'Opening');

            foreach ($availableServers as $server) {
                rollback_dispatch(
                    (new InstallServerFirewallRule($server, $firewallRule,
                        $siteCommand))->onQueue(config('queue.channels.server_commands'))
                );
            }
        }
    }
}
