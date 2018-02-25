<?php

namespace App\Events\Server;

use App\Jobs\Server\Schemas\AddServerSchema;
use App\Jobs\Server\Schemas\AddServerSchemaUser;
use App\Jobs\Server\Schemas\RemoveServerSchema;
use App\Models\Command;
use App\Models\Schema;
use App\Models\Server\Server;
use App\Models\Site\Site;
use App\Services\Systems\SystemService;
use Illuminate\Queue\SerializesModels;

class UpdateServerSchemas
{
    use SerializesModels;

    private $site;
    private $server;
    private $command;
    private $serverType;

    /**
     * Create a new event instance.
     *
     * @param Server  $server
     * @param Site    $site
     * @param Command $siteCommand
     */
    public function __construct(Server $server, Site $site, Command $siteCommand)
    {
        $this->site = $site;
        $this->server = $server;
        $this->command = $siteCommand;
        $this->serverType = $server->type;

        $this->site->schemas->each(function (Schema $schema) {
            if ($this->site->hasDatabaseServers()) {
                if (SystemService::DATABASE_SERVER == $this->serverType) {
                    if (! $schema->hasServer($this->server)) {
                        $this->addSchema($schema);
                    }
                } else {
                    // TODO - we should allow them to migrate from here , so should we remove it? Probably not
                    // $this->removeSchema($schema);
                }
            } elseif (! $schema->hasServer($this->server) && SystemService::FULL_STACK_SERVER == $this->serverType) {
                $this->addSchema($schema);
            }
        });
    }

    /**
     * @param Schema $schema
     */
    private function addSchema(Schema $schema)
    {
        dispatch(
            (new AddServerSchema($this->server, $schema, $this->command))
                ->onQueue(config('queue.channels.server_commands'))
        );

        $this->site->schemaUsers->each(function ($schemaUser) {
            dispatch(
                (new AddServerSchemaUser($this->server, $schemaUser, $this->command))
                    ->onQueue(config('queue.channels.server_commands'))
            );
        });
    }

    /**
     * @param Schema $schema
     */
    private function removeSchema(Schema $schema)
    {
        dispatch(
            (new RemoveServerSchema($this->server, $schema, $this->command))
                ->onQueue(config('queue.channels.server_commands'))
        );
    }
}
