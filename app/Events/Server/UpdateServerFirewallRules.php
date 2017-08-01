<?php

namespace App\Events\Server;

use App\Models\Command;
use App\Models\Site\Site;
use App\Models\FirewallRule;
use App\Models\Server\Server;
use Illuminate\Queue\SerializesModels;
use App\Jobs\Server\FirewallRules\InstallServerFirewallRule;

class UpdateServerFirewallRules
{
    use SerializesModels;

    private $site;
    private $server;
    private $command;
    private $serverType;

    /**
     * Create a new event instance.
     *
     * @param Server $server
     * @param Site $site
     * @param Command $siteCommand
     */
    public function __construct(Server $server, Site $site, Command $siteCommand)
    {
        $this->site = $site;
        $this->server = $server;
        $this->command = $siteCommand;
        $this->serverType = $server->type;

        $this->site->firewallRules->each(function (FirewallRule $firewallRule) {
            $this->installFirewallRule($firewallRule);
        });
    }

    /**
     * @param FirewallRule $firewallRule
     */
    private function installFirewallRule(FirewallRule $firewallRule)
    {
        dispatch(
            (new InstallServerFirewallRule($this->server, $firewallRule, $this->command))->onQueue(config('queue.channels.server_commands'))
        );
    }
}
