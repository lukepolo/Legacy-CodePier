<?php

namespace App\Jobs\Server;

use App\Models\Bitt;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Contracts\Server\ServerServiceContract as ServerService;

class RunBitt implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $bitt;
    private $server;

    public $tries = 1;
    public $timeout = 300;

    /**
     * InstallServerSshKey constructor.
     * @param Server $server
     * @param bitt $bitt
     */
    public function __construct(Server $server, Bitt $bitt)
    {
        $this->bitt = $bitt;
        $this->server = $server;
        $this->makeCommand($server, $bitt, null, 'Running');
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\ServerService | ServerService $serverService
     * @throws \Exception
     */
    public function handle(ServerService $serverService)
    {
        $this->runOnServer(function () use ($serverService) {
            return $serverService->runBitt($this->server, $this->bitt);
        });
    }
}
