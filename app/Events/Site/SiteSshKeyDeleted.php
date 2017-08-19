<?php

namespace App\Events\Site;

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
        $site->sshKeys()->detach($sshKey);

        if ($site->provisionedServers->count()) {
            $siteCommand = $this->makeCommand($site, $sshKey, 'Removing');

            foreach ($site->provisionedServers as $server) {
                rollback_dispatch(
                    (new RemoveServerSshKey($server, $sshKey,
                        $siteCommand))->onQueue(config('queue.channels.server_commands'))
                );
            }
        }
    }
}
