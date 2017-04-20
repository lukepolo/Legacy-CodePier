<?php

namespace App\Events\Site;

use App\Jobs\Server\SshKeys\InstallServerEnvironmentVariable;
use App\Models\EnvironmentVariable;
use App\Models\Site\Site;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;

class SiteEnvironmentVariableCreated
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
        if ($site->provisionedServers->count()) {
            $siteCommand = $this->makeCommand($site, $environmentVariable);

            foreach ($site->provisionedServers as $server) {
                dispatch(
                    (new InstallServerEnvironmentVariable($server, $environmentVariable,
                        $siteCommand))->onQueue(config('queue.channels.server_commands'))
                );
            }
        }
    }
}
