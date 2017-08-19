<?php

namespace App\Events\Site;

use App\Models\Site\Site;
use App\Models\SchemaUser;
use App\Traits\ModelCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Jobs\Server\Schemas\RemoveServerSchemaUser;

class SiteSchemaUserDeleted
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
        $site->schemaUsers()->detach($schemaUser);

        if ($schemaUser->servers()->count()) {
            $siteCommand = $this->makeCommand($site, $schemaUser, 'Deleting');
            foreach ($schemaUser->servers as $server) {
                rollback_dispatch(
                    (new RemoveServerSchemaUser($server, $schemaUser,
                        $siteCommand))->onQueue(config('queue.channels.server_commands'))
                );
            }
        }
    }
}
