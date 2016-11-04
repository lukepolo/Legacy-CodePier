<?php

namespace App\Observers\Site;

use App\Models\Site\SiteFirewallRule;

/**
 * Class SiteFirewallRuleObserver.
 */
class SiteFirewallRuleObserver
{
    public function created(SiteFirewallRule $siteFirewallRule)
    {
    }

    public function deleting(SiteFirewallRule $siteFirewallRule)
    {
    }
}
