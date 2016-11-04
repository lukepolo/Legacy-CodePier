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
     * @return bool
     */
    public function created(ServerFirewallRule $serverFirewallRule)
    {
        if (app()->runningInConsole()) {
            dispatch(new InstallServerFirewallRule($serverFirewallRule));

            return false;
        }
    }

    /**
     * @param ServerFirewallRule $serverFirewallRule
     * @return bool
     */
    public function deleting(ServerFirewallRule $serverFirewallRule)
    {
        if (app()->runningInConsole()) {
            dispatch(new RemoveServerFirewallRule($serverFirewallRule));

            return false;
        }
    }
}
