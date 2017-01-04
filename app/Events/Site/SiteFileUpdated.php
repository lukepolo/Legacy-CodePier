<?php

namespace App\Events\Site;

use App\Jobs\Server\UpdateServerFile;
use App\Models\File;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class SiteFileUpdated
{
    use InteractsWithSockets, SerializesModels, ModelCommandTrait;

    /**
     * Create a new event instance.
     *
     * @param File $file
     */
    public function __construct(File $file)
    {
        foreach ($file->site->provisionedServers as $server) {
            dispatch(
                (new UpdateServerFile($server, $file))->onQueue(env('SERVER_COMMAND_QUEUE'))
            );
          }
    }
}
