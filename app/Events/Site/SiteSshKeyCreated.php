<?php

namespace App\Events\Site;

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
     * @param Site   $site
     * @param SshKey $sshKey
     *
     * @internal param FirewallRule $firewallRule
     */
    public function __construct(Site $site, SshKey $sshKey)
    {
        if ($site->provisionedServers->count()) {
            $siteCommand = $this->makeCommand($site, $sshKey, 'Adding');

            foreach ($site->provisionedServers as $server) {
                dispatch(
                    (new InstallServerSshKey($server, $sshKey, $siteCommand))
                        ->onQueue(config('queue.channels.server_commands'))
                );
            }
        }
    }
}
