<?php

namespace App\Jobs\Server;

use App\Models\Bitt;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Exceptions\ServerCommandFailed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\Server\ServerServiceContract as ServerService;

class RunBitt implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $bitt;
    private $server;

    public $tries = 1;
    public $timeout = 180;

    /**
     * InstallServerSshKey constructor.
     * @param Server $server
     * @param bitt $bitt
     */
    public function __construct(Server $server, Bitt $bitt)
    {
        $this->bitt = $bitt;
        $this->server = $server;
        $this->makeCommand($server, $bitt);
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
        $this->runOnServer(function () use ($serverService) {
            $serverService->runBitt($this->server, $this->bitt);
        });
    }
}
