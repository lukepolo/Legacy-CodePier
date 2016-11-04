<?php

namespace App\Jobs\Server;

use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Exceptions\Traits\ServerErrorTrait;
use App\Models\Server\ServerSshKey;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RemoveServerSshKey implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerErrorTrait;

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
            $serverService->removeSshKey($this->serverSshKey->server, $this->serverSshKey->ssh_key);
        });

        if($this->wasSuccessful()) {
            $this->serverSshKey->delete();
        }

        return $this->remoteResponse();
    }
}
