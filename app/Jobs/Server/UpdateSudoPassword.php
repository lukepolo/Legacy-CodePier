<?php

namespace App\Jobs\Server;

use App\Models\Server\Server;
use Illuminate\Bus\Queueable;
use App\Traits\ServerCommandTrait;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Contracts\Server\ServerServiceContract as ServerService;

class UpdateSudoPassword implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ServerCommandTrait;

    private $server;
    private $newSudoPassword;

    public $tries = 1;
    public $timeout = 15;

    /**
     * Create a new job instance.
     *
     * @param Server $server
     * @param $newSudoPassword
     */
    public function __construct(Server $server, $newSudoPassword)
    {
        $this->server = $server;
        $this->newSudoPassword = $newSudoPassword;

        $this->makeCommand($server, $server, null, 'Updating Sudo Password');
    }

    /**
     * Execute the job.
     *
     * @param ServerService|\App\Services\Server\ServerService $serverService
     * @throws \Exception
     */
    public function handle(ServerService $serverService)
    {
        $this->runOnServer(function () use ($serverService) {
            $serverService->updateSudoPassword($this->server, $this->newSudoPassword);

            if($this->wasSuccessful()) {
                $this->server->sudo_password = $this->newSudoPassword;
                $this->server->save();
            }
        });
    }
}
