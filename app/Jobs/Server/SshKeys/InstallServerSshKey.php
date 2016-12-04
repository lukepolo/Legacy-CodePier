<?php

namespace App\Jobs\Server\SshKeys;

use Illuminate\Bus\Queueable;
use App\Traits\ServerCommandTrait;
use App\Models\Server\ServerSshKey;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Server\ServerServiceContract as ServerService;

class InstallServerSshKey implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $serverSshKey;

    /**
     * InstallServerSshKey constructor.
     * @param ServerSshKey $serverSshKey
     */
    public function __construct(ServerSshKey $serverSshKey)
    {
        $this->makeCommand($serverSshKey);
        $this->serverSshKey = $serverSshKey;
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(ServerService $serverService)
    {
        $this->runOnServer(function () use ($serverService) {
            $serverService->installSshKey($this->serverSshKey->server, $this->serverSshKey->ssh_key);
        });

        if (! $this->wasSuccessful()) {
            $this->serverSshKey->unsetEventDispatcher();
            $this->serverSshKey->delete();
        }

        return $this->remoteResponse();
    }
}
