<?php

namespace App\Events\Site;

use App\Models\Schema;
use App\Models\Site\Site;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Jobs\Server\Schemas\RemoveServerSchema;

class SiteSchemaDeleted
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
        $siteCommand = $this->makeCommand($site, $schema);

        $site->schemas()->detach($schema);

        foreach ($site->provisionedServers as $server) {
            dispatch(
                (new RemoveServerSchema($server, $schema, $siteCommand))->onQueue(config('queue.channels.server_commands'))
            );
        }
    }
}
