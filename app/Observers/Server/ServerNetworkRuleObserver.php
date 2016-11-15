<?php

namespace App\Observers\Server;

use App\Jobs\Server\FirewallRules\InstallServerNetworkRule;
use App\Jobs\Server\FirewallRules\RemoveServerNetworkRule;
use App\Models\Server\ServerNetworkRule;

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
