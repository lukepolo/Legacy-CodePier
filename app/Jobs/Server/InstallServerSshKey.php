<?php

namespace App\Jobs\Server;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Models\Server\ServerSshKey;
use App\Traits\ServerCommandTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
            $this->serverSshKey->delete();
        }

        return $this->remoteResponse();
    }
}
