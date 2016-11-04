<?php

namespace App\Observers\Site;

use App\Models\Site\SiteCronJob;

/**
 * Class SiteCronJobObserver.
 */
class SiteCronJobObserver
{
    /**
     * @param SiteCronJob $siteCronJob
     */
    public function created(SiteCronJob $siteCronJob)
    {
    }

    /**
     * @param SiteCronJob $siteCronJob
     */
    public function deleting(SiteCronJob $siteCronJob)
    {
    }
}
