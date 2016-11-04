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
        if (app()->runningInConsole()) {
            dispatch(new InstallServerFirewallRule($serverFirewallRule));
        }
    }

    /**
     * @param ServerFirewallRule $serverFirewallRule
     */
    public function deleted(ServerFirewallRule $serverFirewallRule)
    {
        if (app()->runningInConsole()) {
            dispatch(new RemoveServerFirewallRule($serverFirewallRule));
        }
    }
}
