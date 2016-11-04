<?php

namespace App\Observers\Server;

use App\Jobs\Server\InstallServerFirewallRule;
use App\Jobs\Server\RemoveServerFirewallRule;
use App\Models\Server\ServerFirewallRule;

/**
 * Class ServerFirewallRuleObserver.
 */
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
     */
    public function deleting(ServerFirewallRule $serverFirewallRule)
    {
        dispatch(new RemoveServerFirewallRule($serverFirewallRule));
    }
}
