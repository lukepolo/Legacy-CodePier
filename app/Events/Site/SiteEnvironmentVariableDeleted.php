<?php

namespace App\Events\Site;

use App\Jobs\Server\SshKeys\RemoveServerEnvironmentVariable;
use App\Models\EnvironmentVariable;
use App\Models\Site\Site;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;

class SiteEnvironmentVariableDeleted
{
    use SerializesModels, ModelCommandTrait;

    /**
     * Create a new event instance.
     *
     * @param Site $site
     * @param EnvironmentVariable $environmentVariable
     */
    public function __construct(Site $site, EnvironmentVariable $environmentVariable)
    {
        $site->firewallRules()->detach($environmentVariable);

        if ($site->provisionedServers->count()) {
            $siteCommand = $this->makeCommand($site, $environmentVariable);

            foreach ($site->provisionedServers as $server) {
                dispatch(
                    (new RemoveServerEnvironmentVariable($server, $environmentVariable,
                        $siteCommand))->onQueue(config('queue.channels.server_commands'))
                );
            }
        }
    }
}
