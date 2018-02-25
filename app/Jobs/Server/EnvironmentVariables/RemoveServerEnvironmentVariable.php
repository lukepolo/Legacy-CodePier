<?php

namespace App\Jobs\Server\EnvironmentVariables;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Models\Command;
use App\Models\EnvironmentVariable;
use App\Models\Server\Server;
use App\Traits\ServerCommandTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveServerEnvironmentVariable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $server;
    private $environmentVariable;

    public $tries = 1;
    public $timeout = 60;

    /**
     * InstallServerSshKey constructor.
     *
     * @param Server              $server
     * @param EnvironmentVariable $environmentVariable
     * @param Command             $siteCommand
     */
    public function __construct(Server $server, EnvironmentVariable $environmentVariable, Command $siteCommand = null)
    {
        $this->server = $server;
        $this->environmentVariable = $environmentVariable;
        $this->makeCommand($server, $environmentVariable, $siteCommand, 'Removing');
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
        $sitesCount = $this->environmentVariable->sites->count();

        if (! $sitesCount) {
            $this->runOnServer(function () use ($serverService) {
                $serverService->removeEnvironmentVariable($this->server, $this->environmentVariable);
            });

            if ($this->wasSuccessful()) {
                $this->server->environmentVariables()->detach($this->environmentVariable->id);

                $this->environmentVariable->load('servers');
                if (0 == $this->environmentVariable->servers->count()) {
                    $this->environmentVariable->delete();
                }
            }
        } else {
            $this->updateServerCommand(0, 'Sites that are on this server using this environment variable', false);
        }
    }
}
