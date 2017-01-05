<?php

namespace App\Jobs\Server\SshKeys;

use App\Models\SshKey;
use App\Models\Command;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Exceptions\ServerCommandFailed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Server\ServerServiceContract as ServerService;

class RemoveServerSshKey implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $server;
    private $sshKey;

    /**
     * InstallServerSshKey constructor.
     * @param Server $server
     * @param SshKey $sshKey
     * @param Command $siteCommand
     */
    public function __construct(Server $server, SshKey $sshKey, Command $siteCommand = null)
    {
        $this->server = $server;
        $this->sshKey = $sshKey;
        $this->makeCommand($server, $sshKey, $siteCommand);
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
        $sitesCount = $this->sshKey->sites->count();

        if(!$sitesCount) {
            $this->runOnServer(function () use ($serverService) {
                $serverService->removeSshKey($this->server, $this->sshKey);
            });

            if (! $this->wasSuccessful()) {
                throw new ServerCommandFailed($this->getCommandErrors());
            }
        }

        $this->server->sshKeys()->detach($this->sshKey->id);

        if(!$sitesCount) {
            $this->sshKey->load('servers');
            if ($this->sshKey->servers->count() == 0) {
                $this->sshKey->delete();
            }
        }
    }
}
