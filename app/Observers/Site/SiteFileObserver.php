<?php

namespace App\Observers\Site;

use App\Jobs\Server\UpdateServerFile;
use App\Models\Site\SiteFile;

class SiteFileObserver
{
    /**
     * @param SiteFile $siteFile
     */
    public function created(SiteFile $siteFile)
    {
        dispatch(new UpdateServerFile($siteFile));
    }

    public function updated(SiteFile $siteFile)
    {
        dispatch(new UpdateServerFile($siteFile));
    }
}
