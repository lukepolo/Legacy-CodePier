<?php

namespace App\Jobs\Server\Schemas;

use App\Models\Schema;
use App\Models\Command;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Exceptions\ServerCommandFailed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Server\ServerServiceContract as ServerService;

class RemoveServerSchema implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $server;
    private $schema;

    public $tries = 1;
    public $timeout = 60;

    /**
     * Create a new job instance.
     * @param Server $server
     * @param Schema $schema
     * @param Command $siteCommand
     */
    public function __construct(Server $server, Schema $schema, Command $siteCommand = null)
    {
        $this->server = $server;
        $this->schema = $schema;
        $this->makeCommand($server, $schema, $siteCommand);
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @return \Illuminate\Http\JsonResponse
     * @throws ServerCommandFailed
     */
    public function handle(ServerService $serverService)
    {
        $sitesCount = $this->schema->sites->count();

        if (! $sitesCount) {
            $this->runOnServer(function () use ($serverService) {
                $serverService->removeSchema($this->server, $this->schema);
            });

            $this->server->schemas()->detach($this->schema->id);

            $this->schema->load('servers');
            if ($this->schema->servers->count() == 0) {
                $this->schema->delete();
            }
        } else {
            $this->updateServerCommand(0, 'Sites that are on this server are using this schema', false);
        }
    }
}
