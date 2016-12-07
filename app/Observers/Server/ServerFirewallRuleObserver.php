<?php

namespace App\Observers\Server;

use App\Models\Server\ServerFirewallRule;
use App\Jobs\Server\FirewallRules\RemoveServerFirewallRule;
use App\Jobs\Server\FirewallRules\InstallServerFirewallRule;

class ServerFirewallRuleObserver
{
    /**
     * @param ServerFirewallRule $serverFirewallRule
     */
    public function created(ServerFirewallRule $serverFirewallRule)
    {
        dispatch(new InstallServerFirewallRule($serverFirewallRule));
    }

    /**
     * @param ServerFirewallRule $serverFirewallRule
     * @return bool
     */
    public function deleting(ServerFirewallRule $serverFirewallRule)
    {
        dispatch(new RemoveServerFirewallRule($serverFirewallRule));

        return false;
    }
}
