<?php

namespace App\Observers;

use App\Models\Site;

/**
 * Class SiteObserver
 * @package App\Observers
 */
class SiteObserver
{
    static $originalServers = [];

    public function saved(Site $site)
    {

    }

}