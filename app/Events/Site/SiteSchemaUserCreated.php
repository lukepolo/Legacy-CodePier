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
        $availableServers = $site->filterServerByType([
            SystemService::DATABASE_SERVER,
            SystemService::FULL_STACK_SERVER
        ]);

        if($availableServers->count()) {
            $siteCommand = $this->makeCommand($site, $schemaUser, 'Creating');

            foreach ($availableServers as $server) {
                dispatch(
                    (new AddServerSchemaUser($server, $schemaUser,
                        $siteCommand))->onQueue(config('queue.channels.server_commands'))
                );
            }
        }
    }
}
