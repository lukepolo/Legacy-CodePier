<?php

namespace App\Events\Sites;

use App\Models\SshKey;
use App\Models\Site\Site;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Jobs\Server\SshKeys\RemoveServerSshKey;

class SiteSshKeyDeleted
{
    use SerializesModels, ModelCommandTrait;

    /**
     * Create a new event instance.
     *
     * @param Site $site
     * @param SshKey $sshKey
     * @internal param FirewallRule $firewallRule
     */
    public function __construct(Site $site, SshKey $sshKey)
    {
        $siteCommand = $this->makeCommand($site, $sshKey);

        foreach ($site->provisionedServers as $server) {
            dispatch(
                (new RemoveServerSshKey($server, $sshKey, $siteCommand))->onQueue(env('SERVER_COMMAND_QUEUE'))
            );
        }
    }
}
