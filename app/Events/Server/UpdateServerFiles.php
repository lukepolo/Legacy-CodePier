<?php

namespace App\Events\Server;

use App\Models\File;
use App\Models\Command;
use App\Models\Site\Site;
use App\Models\Server\Server;
use App\Jobs\Server\UpdateServerFile;
use Illuminate\Queue\SerializesModels;

class UpdateServerFiles
{
    use SerializesModels;

    private $site;
    private $server;
    private $command;
    private $serverType;

    /**
     * Create a new event instance.
     *
     * @param Server  $server
     * @param Site    $site
     * @param Command $siteCommand
     */
    public function __construct(Server $server, Site $site, Command $siteCommand)
    {
        $this->site = $site;
        $this->server = $server;
        $this->command = $siteCommand;
        $this->serverType = $server->type;

        $hasMultipleServers = $site->servers->count() > 1 ? true : false;

        $this->site->files->each(function (File $file) use ($hasMultipleServers) {
            if (! empty($file->content)) {
                if (! $hasMultipleServers || true === $file->custom || true === $file->framework_file) {
                    $this->updateFile($file);
                }
            }
        });
    }

    /**
     * @param File $file
     */
    private function updateFile(File $file)
    {
        dispatch(
            (new UpdateServerFile($this->server, $file, $this->command))
                ->onQueue(config('queue.channels.server_commands'))
        );
    }
}
