<?php

namespace App\Events\Site;

use App\Jobs\Server\UpdateServerFile;
use App\Models\File;
use App\Models\Site\Site;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;

class SiteFileUpdated
{
    use SerializesModels, ModelCommandTrait;

    /**
     * Create a new event instance.
     *
     * @param Site $site
     * @param File $file
     */
    public function __construct(Site $site, File $file)
    {
        if ($site->provisionedServers->count()) {
            $siteCommand = $this->makeCommand($site, $file, 'Updating');

            foreach ($site->provisionedServers as $server) {
                dispatch(
                    (new UpdateServerFile($server, $file, $siteCommand))
                        ->onQueue(config('queue.channels.server_commands'))
                );
            }
        }
    }
}
