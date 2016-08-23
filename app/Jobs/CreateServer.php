<?php

namespace App\Jobs;

use App\Contracts\Server\ServerServiceContract;
use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Events\Server\ServerProvisionStatusChanged;
use App\Models\Server;
use App\Models\ServerProvider;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class CreateServer
 * @package App\Jobs
 */
class CreateServer implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, DispatchesJobs;

    protected $server;
    protected $options;
    protected $serverProvider;

    /**
     * Create a new job instance.
     * @param ServerProvider $serverProvider
     * @param Server $server
     */
    public function __construct(ServerProvider $serverProvider, Server $server)
    {
        $this->server = $server;
        $this->serverProvider = $serverProvider;
    }

    /**
     * Execute the job.
     * @param \App\Services\Server\ServerService | ServerServiceContract $serverService
     * @throws \Exception
     */
    public function handle(ServerService $serverService)
    {
        event(new ServerProvisionStatusChanged($this->server, 'Creating Server', 0));

        /** @var Server $server */
        $server = $serverService->create($this->serverProvider, $this->server);

        $serverStatus = 'new';

        while ($serverStatus == 'new') {
            sleep(5);
            $serverStatus = $serverService->getStatus($server);
        }

        event(new ServerProvisionStatusChanged($server, 'Server Created', 0));

        $serverService->saveInfo($server);

        $sshConnection = false;

        while ($sshConnection == false) {
            sleep(5);
            $sshConnection = $serverService->testSshConnection($server);
        }

        event(new ServerProvisionStatusChanged($server, 'Queue for Provisioning', 0));

        dispatch(new ProvisionServer($server));
//            ->onQueue('server_provision'));
    }
}
