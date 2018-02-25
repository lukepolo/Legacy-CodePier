<?php

namespace App\Jobs\Server\Schemas;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Models\Command;
use App\Models\Schema;
use App\Models\Server\Server;
use App\Traits\ServerCommandTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveServerSchema implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $server;
    private $schema;

    public $tries = 1;
    public $timeout = 60;

    /**
     * Create a new job instance.
     *
     * @param Server  $server
     * @param Schema  $schema
     * @param Command $siteCommand
     */
    public function __construct(Server $server, Schema $schema, Command $siteCommand = null)
    {
        $this->server = $server;
        $this->schema = $schema;
        $this->makeCommand($server, $schema, $siteCommand, 'Removing');
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     *
     * @throws \Exception
     */
    public function handle(ServerService $serverService)
    {
        $sitesCount = $this->schema->sites->count();

        if (! $sitesCount) {
            $this->runOnServer(function () use ($serverService) {
                $serverService->removeSchema($this->server, $this->schema);
            });

            if ($this->wasSuccessful()) {
                $this->server->schemas()->detach($this->schema->id);

                $this->schema->load('servers');
                if (0 == $this->schema->servers->count()) {
                    $this->schema->delete();
                }
            }
        } else {
            $this->updateServerCommand(0, 'Sites that are on this server are using this schema', false);
        }
    }
}
