<?php

namespace App\Jobs\Server\Schemas;

use App\Models\Command;
use App\Models\SchemaUser;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Exceptions\ServerCommandFailed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Server\ServerServiceContract as ServerService;

class RemoveServerSchemaUser implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $server;
    private $schemaUser;

    public $tries = 1;
    public $timeout = 60;

    /**
     * Create a new job instance.
     * @param Server $server
     * @param SchemaUser $schemaUser
     * @param Command $siteCommand
     */
    public function __construct(Server $server, SchemaUser $schemaUser, Command $siteCommand = null)
    {
        $this->server = $server;
        $this->schemaUser = $schemaUser;
        $this->makeCommand($server, $schemaUser, $siteCommand, 'Removing');
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
        $sitesCount = $this->schemaUser->sites->count();

        if (! $sitesCount) {
            $this->runOnServer(function () use ($serverService) {
                $serverService->removeSchemaUser($this->server, $this->schemaUser);
            });

            if (! $this->wasSuccessful()) {
                throw new ServerCommandFailed($this->getCommandErrors());
            }

            $this->server->schemaUsers()->detach($this->schemaUser->id);

            $this->schemaUser->load('servers');
            if ($this->schemaUser->servers->count() == 0) {
                $this->schemaUser->delete();
            }
        } else {
            $this->updateServerCommand(0, 'Sites that are on this server are using this schema user', false);
        }
    }
}
