<?php

namespace App\Observers\Site;

use App\Models\Site\SiteSshKey;

/**
 * Class SiteSshKeyObserver.
 */
class SiteSshKeyObserver
{
    /**
     * @param SiteSshKey $siteSshKey
     */
    public function created(SiteSshKey $siteSshKey)
    {
    }

    /**
     * @param SiteSshKey $siteSshKey
     */
    public function deleting(SiteSshKey $siteSshKey)
    {
    }
}
