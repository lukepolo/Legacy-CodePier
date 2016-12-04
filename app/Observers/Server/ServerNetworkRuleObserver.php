<?php

namespace App\Observers\Server;

use App\Models\Server\ServerNetworkRule;
use App\Jobs\Server\FirewallRules\RemoveServerNetworkRule;
use App\Jobs\Server\FirewallRules\InstallServerNetworkRule;

class ServerNetworkRuleObserver
{
    /**
     * @param ServerNetworkRule $serverNetworkRule
     */
    public function created(ServerNetworkRule $serverNetworkRule)
    {
        dispatch(new InstallServerNetworkRule($serverNetworkRule));
    }

    /**
     * @param ServerNetworkRule $serverNetworkRule
     */
    public function deleting(ServerNetworkRule $serverNetworkRule)
    {
        dispatch(new RemoveServerNetworkRule($serverNetworkRule));
    }
}
