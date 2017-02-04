<?php

namespace App\Events\Site;

use App\Models\Site\Site;
use App\Models\SchemaUser;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Jobs\Server\Schemas\UpdateServerSchemaUser;

class SiteSchemaUserUpdated
{
    use SerializesModels, ModelCommandTrait;

    /**
     * Create a new event instance.
     *
     * @param Site $site
     * @param SchemaUser $schemaUser
     */
    public function __construct(Site $site, SchemaUser $schemaUser)
    {
        $siteCommand = $this->makeCommand($site, $schemaUser);

        foreach ($site->provisionedServers as $server) {
            dispatch(
                (new UpdateServerSchemaUser($server, $schemaUser, $siteCommand))->onQueue(config('queue.channels.server_commands'))
            );
        }
    }
}
