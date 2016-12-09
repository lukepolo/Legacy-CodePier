<?php

namespace App\Observers\Site;

use App\Models\Site\SiteFile;
use App\Traits\ModelCommandTrait;
use App\Jobs\Server\UpdateServerFile;

class SiteFileObserver
{
    use ModelCommandTrait;

    /**
     * @param SiteFile $siteFile
     */
    public function created(SiteFile $siteFile)
    {
        foreach ($siteFile->site->provisionedServers as $server) {
            dispatch(
                (new UpdateServerFile($server, $siteFile))->onQueue(env('SERVER_COMMAND_QUEUE'))
            );
        }
    }

    public function updated(SiteFile $siteFile)
    {
        foreach ($siteFile->site->provisionedServers as $server) {
            dispatch(
                (new UpdateServerFile($server, $siteFile))->onQueue(env('SERVER_COMMAND_QUEUE'))
            );
        }
    }
}
