<?php

namespace App\Observers\Server;

use App\Jobs\Server\FirewallRules\InstallServerFirewallRule;
use App\Jobs\Server\FirewallRules\RemoveServerFirewallRule;
use App\Models\Server\ServerFirewallRule;

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
