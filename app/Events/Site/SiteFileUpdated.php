<?php

namespace App\Events\Site;

use App\Models\File;
use App\Models\Site\Site;
use App\Traits\ModelCommandTrait;
use App\Jobs\Server\UpdateServerFile;
use Illuminate\Queue\SerializesModels;

class SiteFileUpdated
{
    use SerializesModels, ModelCommandTrait;

    /**
     * Create a new event instance.
     *
     * @param Site $site
     * @param File $file
     * @param boolean|null $shouldFlushLaravelConfigCache
     */
    public function __construct(Site $site, File $file, $shouldFlushLaravelConfigCache = false)
    {
        if ($site->provisionedServers->count()) {
            $status = 'Updating';
            $shouldFlushLaravelConfigCache ? $status .= ' (and refreshing config cache for)' : null;
            $siteCommand = $this->makeCommand($site, $file, $status);

            foreach ($site->provisionedServers as $server) {
                dispatch(
                    (new UpdateServerFile($server, $file, $siteCommand, $site, $shouldFlushLaravelConfigCache))
                        ->onQueue(config('queue.channels.server_commands'))
                );
            }
        }
    }
}
