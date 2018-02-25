<?php

namespace App\Jobs\Server\Buoys;

use App\Contracts\BuoyServiceContract as BuoyService;
use App\Models\Buoy;
use App\Models\Command;
use App\Models\Server\Server;
use App\Traits\ServerCommandTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveBuoy implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $buoy;
    private $server;

    public $tries = 1;
    public $timeout = 60;

    /**
     * InstallServerWorker constructor.
     *
     * @param Server  $server
     * @param Buoy    $buoy
     * @param Command $siteCommand
     */
    public function __construct(Server $server, Buoy $buoy, Command $siteCommand = null)
    {
        $this->server = $server;
        $this->buoy = $buoy;
        $this->makeCommand($server, $buoy, $siteCommand, 'Removing');
    }

    /**
     * @param \App\Services\Buoys\BuoyService | BuoyService $buoyService
     *
     * @throws \Exception
     */
    public function handle(BuoyService $buoyService)
    {
        $this->runOnServer(function () use ($buoyService) {
            $buoyService->removeBuoy($this->server, $this->buoy);
        });

        if ($this->wasSuccessful()) {
            $this->server->buoys()->detach($this->buoy);
        }
    }
}
