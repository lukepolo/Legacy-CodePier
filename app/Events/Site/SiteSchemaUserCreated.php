<?php

namespace App\Events\Site;

use App\Models\Site\Site;
use App\Models\SchemaUser;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Services\Systems\SystemService;
use App\Jobs\Server\Schemas\AddServerSchemaUser;

class SiteSchemaUserCreated
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
        if ($site->provisionedServers->count()) {
            $siteCommand = $this->makeCommand($site, $schemaUser, 'Creating');

            foreach ($site->provisionedServers as $server) {
                $serverType = $server->type;

                if (
                    $serverType === SystemService::DATABASE_SERVER ||
                    $serverType === SystemService::FULL_STACK_SERVER
                ) {
                    dispatch(
                        (new AddServerSchemaUser($server, $schemaUser,
                            $siteCommand))->onQueue(config('queue.channels.server_commands'))
                    );
                }
            }
        }
    }
}
