<?php

namespace App\Jobs\Server\Buoys;

use App\Models\Buoy;
use App\Models\Command;
use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use App\Exceptions\ServerCommandFailed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Contracts\BuoyServiceContract as BuoyService;

class InstallBuoy implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $buoy;
    private $server;

    public $tries = 1;
    public $timeout = 600;

    /**
     * InstallServerWorker constructor.
     * @param Server $server
     * @param Buoy $buoy
     * @param Command $siteCommand
     */
    public function __construct(Server $server, Buoy $buoy, Command $siteCommand = null)
    {
        $this->server = $server;
        $this->buoy = $buoy;
        $this->makeCommand($server, $buoy, $siteCommand);
    }

    /**
     * @param \App\Services\Buoys\BuoyService | BuoyService $buoyService
     * @throws ServerCommandFailed
     */
    public function handle(BuoyService $buoyService)
    {
        if ($this->server->buoys->keyBy('id')->get($this->buoy->id)) {
            $this->updateServerCommand(0, 'Sever already has buoy installed.');
        } else {
            $this->runOnServer(function () use ($buoyService) {
                $buoyService->installBuoy($this->server, $this->buoy);
            });

            if (! $this->wasSuccessful()) {
                throw new ServerCommandFailed($this->getCommandErrors());
            }

            $this->server->buoys()->save($this->buoy);
        }
    }
}
