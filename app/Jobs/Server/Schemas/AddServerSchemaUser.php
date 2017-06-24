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

class AddServerSchemaUser implements ShouldQueue
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
     * @internal param Schema $schema
     */
    public function __construct(Server $server, SchemaUser $schemaUser, Command $siteCommand = null)
    {
        $this->server = $server;
        $this->schemaUser = $schemaUser;
        $this->makeCommand($server, $schemaUser, $siteCommand, 'Adding');
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
        if (
            $this->server->schemaUsers
            ->where('name', $this->schemaUser->name)
            ->count()
            ||
            $this->server->schemaUsers->keyBy('id')->get($this->schemaUser->id)
        ) {
            $this->updateServerCommand(0, 'Sever already has schema user : '.$this->schemaUser->name);
        } else {
            $this->runOnServer(function () use ($serverService) {
                $serverService->addSchemaUser($this->server, $this->schemaUser);
            });

            if (! $this->wasSuccessful()) {
                throw new ServerCommandFailed($this->getCommandErrors());
            }

            $this->server->schemaUsers()->save($this->schemaUser);
        }
    }
}
