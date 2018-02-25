<?php

namespace App\Events\Server;

use App\Jobs\Server\EnvironmentVariables\InstallServerEnvironmentVariable;
use App\Models\Command;
use App\Models\EnvironmentVariable;
use App\Models\Server\Server;
use App\Models\Site\Site;
use Illuminate\Queue\SerializesModels;

class UpdateServerEnvironmentVariables
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

        $this->site->environmentVariables->each(function (EnvironmentVariable $environmentVariable) {
            $this->setEnvironmentVariable($environmentVariable);
        });
    }

    /**
     * @param EnvironmentVariable $environmentVariable
     */
    private function setEnvironmentVariable(EnvironmentVariable $environmentVariable)
    {
        dispatch(
            (new InstallServerEnvironmentVariable($this->server, $environmentVariable, $this->command))
                ->onQueue(config('queue.channels.server_commands'))
        );
    }
}
