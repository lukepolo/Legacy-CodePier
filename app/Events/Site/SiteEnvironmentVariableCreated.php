<?php

namespace App\Events\Site;

use App\Models\Site\Site;
use App\Traits\ModelCommandTrait;
use App\Models\EnvironmentVariable;
use Illuminate\Queue\SerializesModels;
use App\Jobs\Server\EnvironmentVariables\InstallServerEnvironmentVariable;

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

            $siteCommand = $this->makeCommand($site, $environmentVariable, 'Adding');

            foreach ($site->provisionedServers as $server) {
                dispatch(
                    (new InstallServerEnvironmentVariable($server, $environmentVariable,
                        $siteCommand))->onQueue(config('queue.channels.server_commands'))
                );
            }
        }
    }
}
