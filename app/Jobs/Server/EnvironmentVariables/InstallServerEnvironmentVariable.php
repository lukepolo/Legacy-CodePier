<?php

namespace App\Jobs\Server\EnvironmentVariables;

use App\Models\Command;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Traits\ServerCommandTrait;
use App\Models\EnvironmentVariable;
use Illuminate\Queue\SerializesModels;
use App\Exceptions\ServerCommandFailed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Contracts\Server\ServerServiceContract as ServerService;

class InstallServerEnvironmentVariable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $server;
    private $environmentVariable;

    public $tries = 1;
    public $timeout = 60;

    /**
     * InstallServerSshKey constructor.
     * @param Server $server
     * @param EnvironmentVariable $environmentVariable
     * @param Command $siteCommand
     */
    public function __construct(Server $server, EnvironmentVariable $environmentVariable, Command $siteCommand = null)
    {
        $this->server = $server;
        $this->environmentVariable = $environmentVariable;
        $this->makeCommand($server, $environmentVariable, $siteCommand, 'Adding');
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @throws \Exception
     */
    public function handle(ServerService $serverService)
    {
        if ($this->server->environmentVariables
            ->where('variable', $this->environmentVariable->variable)
            ->count()
            ||
            $this->server->environmentVariables->keyBy('id')->get($this->environmentVariable->id)
        ) {
            $this->updateServerCommand(0, 'Sever already has this environment variable');
        } else {
            $this->runOnServer(function () use ($serverService) {
                $serverService->addEnvironmentVariable($this->server, $this->environmentVariable);
            });

            if ($this->wasSuccessful()) {
                $this->server->environmentVariables()->save($this->environmentVariable);
            }
        }
    }
}
