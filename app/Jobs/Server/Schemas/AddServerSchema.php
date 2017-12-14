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
use Illuminate\Foundation\Bus\Dispatchable;
use App\Contracts\Server\ServerServiceContract as ServerService;

class AddServerSchema implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

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
        $this->makeCommand($server, $schema, $siteCommand, 'Adding');
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @throws \Exception
     */
    public function handle(ServerService $serverService)
    {
        if (
            $this->server->schemas
            ->where('name', $this->schema->name)
            ->where('database', $this->schema->database)
            ->count()
            ||
            $this->server->schemas->keyBy('id')->get($this->schema->id)
        ) {
            $this->updateServerCommand(0, 'Sever already has schema created : '.$this->schema->name);
        } else {
            $this->runOnServer(function () use ($serverService) {
                $serverService->addSchema($this->server, $this->schema);
            });

            if ($this->wasSuccessful()) {
                $this->server->schemas()->save($this->schema);
            }
        }
    }
}
