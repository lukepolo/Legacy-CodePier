<?php

namespace App\Observers\Server;

use App\Models\Server\ServerNetworkRule;

/**
 * Class ServerNetworkRuleObserver.
 */
class ServerNetworkRuleObserver
{
    /**
     * @param ServerNetworkRule $serverNetworkRule
     */
    public function created(ServerNetworkRule $serverNetworkRule)
    {
    }

    /**
     * @param ServerNetworkRule $serverNetworkRule
     */
    public function deleting(ServerNetworkRule $serverNetworkRule)
    {
    }
}
