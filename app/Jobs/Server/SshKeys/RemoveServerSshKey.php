<?php

namespace App\Jobs\Server\SshKeys;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Models\Command;
use App\Models\Server\Server;
use App\Models\SshKey;
use App\Traits\ServerCommandTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveServerSshKey implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $server;
    private $sshKey;

    public $tries = 1;
    public $timeout = 60;

    /**
     * InstallServerSshKey constructor.
     *
     * @param Server  $server
     * @param SshKey  $sshKey
     * @param Command $siteCommand
     */
    public function __construct(Server $server, SshKey $sshKey, Command $siteCommand = null)
    {
        $this->server = $server;
        $this->sshKey = $sshKey;
        $this->makeCommand($server, $sshKey, $siteCommand, 'Removing');
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
        $sitesCount = $this->sshKey->sites->count();

        if (! $sitesCount) {
            $this->runOnServer(function () use ($serverService) {
                $serverService->removeSshKey($this->server, $this->sshKey);
            });

            if ($this->wasSuccessful()) {
                $this->server->sshKeys()->detach($this->sshKey->id);

                $this->sshKey->load('servers');
                if (0 == $this->sshKey->servers->count()) {
                    $this->sshKey->delete();
                }
            }
        } else {
            $this->updateServerCommand(0, 'Sites that are on this server using this ssh key', false);
        }
    }
}
