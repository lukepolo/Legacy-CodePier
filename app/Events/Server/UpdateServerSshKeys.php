<?php

namespace App\Events\Server;

use App\Models\SshKey;
use App\Models\Command;
use App\Models\Site\Site;
use App\Models\Server\Server;
use Illuminate\Queue\SerializesModels;
use App\Jobs\Server\SshKeys\InstallServerSshKey;

class UpdateServerSshKeys
{
    use SerializesModels;

    private $site;
    private $server;
    private $command;
    private $serverType;

    /**
     * Create a new event instance.
     *
     * @param Server $server
     * @param Site $site
     * @param Command $siteCommand
     */
    public function __construct(Server $server, Site $site, Command $siteCommand)
    {
        $this->site = $site;
        $this->server = $server;
        $this->command = $siteCommand;
        $this->serverType = $server->type;

        $this->site->sshKeys->each(function (SshKey $sshKey) {
            if(!$sshKey->hasServer($this->server)) {
                $this->installSshKey($sshKey);
            }
        });
    }

    /**
     * @param SshKey $sshKey
     */
    private function installSshKey(SshKey $sshKey)
    {
        dispatch(
            (new InstallServerSshKey($this->server, $sshKey, $this->command))->onQueue(config('queue.channels.server_commands'))
        );
    }

}
