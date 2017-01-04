<?php

namespace App\Events\Sites;

use App\Jobs\Server\SshKeys\InstallServerSshKey;
use App\Models\Site\Site;
use App\Models\SshKey;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;

class SiteSshKeyCreated
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
            if (! $server->sshKeys
                ->where('ssh_key', $sshKey->ssh_key)
                ->count()
            ) {
                dispatch(
                    (new InstallServerSshKey($server, $sshKey, $siteCommand))->onQueue(env('SERVER_COMMAND_QUEUE'))
                );
            }
        }
    }
}
