<?php

namespace App\Events\Site;

use App\Models\Schema;
use App\Models\Site\Site;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Services\Systems\SystemService;
use App\Jobs\Server\Schemas\AddServerSchema;

class SiteSchemaCreated
{
    use SerializesModels, ModelCommandTrait;

    /**
     * Create a new event instance.
     *
     * @param Site $site
     * @param Schema $schema
     */
    public function __construct(Site $site, Schema $schema)
    {
        $availableServers = $site->filterServerByType([
            SystemService::DATABASE_SERVER,
            SystemService::FULL_STACK_SERVER
        ]);

        if($availableServers->count()) {
            $siteCommand = $this->makeCommand($site, $schema, 'Creating');

            foreach ($availableServers as $server) {
                dispatch(
                    (new AddServerSchema($server, $schema,
                        $siteCommand))->onQueue(config('queue.channels.server_commands'))
                );
            }
        }

    }
}
