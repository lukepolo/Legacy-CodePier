<?php

namespace App\Events\Sites;

use App\Jobs\Server\SshKeys\RemoveServerSshKey;
use App\Models\Site\Site;
use App\Models\SshKey;
use App\Traits\ModelCommandTrait;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class SiteSshKeyDeleted
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
            dispatch(
                (new RemoveServerSshKey($server, $sshKey, $this->makeCommand($site, $sshKey)))->onQueue(env('SERVER_COMMAND_QUEUE'))
            );
        }
    }
}
