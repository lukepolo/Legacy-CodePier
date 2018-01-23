<?php

namespace App\Jobs\Server\SshKeys;

use App\Models\SshKey;
use App\Models\Command;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Contracts\Server\ServerServiceContract as ServerService;

class InstallServerSshKey implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $server;
    private $sshKey;

    public $tries = 1;
    public $timeout = 60;

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
        $this->makeCommand($server, $sshKey, $siteCommand, 'Installing');
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @throws \Exception
     */
    public function handle(ServerService $serverService)
    {
        if ($this->server->sshKeys
            ->where('ssh_key', $this->sshKey->ssh_key)
            ->count()
            ||
            $this->server->sshKeys->keyBy('id')->get($this->sshKey->id)
        ) {
            $this->updateServerCommand(0, 'Sever already has the ssh key');
        } else {
            $this->runOnServer(function () use ($serverService) {
                $serverService->installSshKey($this->server, $this->sshKey);
            });

            if ($this->wasSuccessful()) {
                $this->server->sshKeys()->save($this->sshKey);
            }
        }
    }
}
