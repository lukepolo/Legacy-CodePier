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
     * @param File $file
     */
    public function __construct(Site $site, File $file)
    {
        $siteCommand = $this->makeCommand($site, $file);

        foreach ($file->site->provisionedServers as $server) {
            dispatch(
                (new UpdateServerFile($server, $file, $siteCommand))->onQueue(env('SERVER_COMMAND_QUEUE'))
            );
        }
    }
}
