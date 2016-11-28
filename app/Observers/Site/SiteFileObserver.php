<?php

namespace App\Observers\Site;

use App\Jobs\Server\UpdateServerFile;
use App\Models\Site\SiteFile;
use App\Traits\ModelCommandTrait;

class SiteFileObserver
{
    use ModelCommandTrait;

    /**
     * @param SiteFile $siteFile
     */
    public function created(SiteFile $siteFile)
    {
        foreach ($siteFile->site->provisionedServers as $server) {
            dispatch(new UpdateServerFile($server, $siteFile));
        }
    }

    public function updated(SiteFile $siteFile)
    {
        foreach ($siteFile->site->provisionedServers as $server) {
            dispatch(new UpdateServerFile($server, $siteFile));
        }
    }
}
