<?php

namespace App\Jobs\Server\SshKeys;

use App\Models\EnvironmentVariable;
use App\Models\Command;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Exceptions\ServerCommandFailed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Server\ServerServiceContract as ServerService;

class InstallServerEnvironmentVariable implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

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
        $this->makeCommand($server, $environmentVariable, $siteCommand);
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

            if (! $this->wasSuccessful()) {
                throw new ServerCommandFailed($this->getCommandErrors());
            }

            $this->server->environmentVariables()->save($this->environmentVariable);
        }
    }
}
