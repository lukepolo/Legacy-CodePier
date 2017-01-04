<?php

namespace App\Events\Sites;

use App\Jobs\Server\SshKeys\InstallServerSshKey;
use App\Models\Site\Site;
use App\Models\SshKey;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;

class SiteSshKeyCreated
{
    use InteractsWithSockets, SerializesModels, ModelCommandTrait;

    /**
     * Create a new event instance.
     *
     * @param Site $site
     * @param SshKey $sshKey
     * @internal param FirewallRule $firewallRule
     */
    public function __construct(Site $site, SshKey $sshKey)
    {
        foreach ($site->provisionedServers as $server) {
            if (! $server->sshKeys
                ->where('ssh_key', $sshKey->ssh_key)
                ->count()
            ) {
                dispatch(
                    (new InstallServerSshKey($server, $sshKey, $this->makeCommand($site, $sshKey)))->onQueue(env('SERVER_COMMAND_QUEUE'))
                );
            }
        }
    }
}
