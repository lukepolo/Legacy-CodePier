<?php

namespace App\Observers\Site;

use App\Models\Site\SiteFile;

/**
 * Class SiteFileObserver.
 */
class SiteFileObserver
{
    /**
     * @param SiteFile $siteFile
     */
    public function created(SiteFile $siteFile)
    {

    }


    /**
     * @param SiteFile $siteFile
     */
    public function deleting(SiteFile $siteFile)
    {

    }
}
