<?php

namespace App\Jobs;

use App\Contracts\Server\ServerServiceContract;
use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Events\ServerCreated;
use App\Models\UserServer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateServer extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, DispatchesJobs;

    protected $options;
    protected $service;

    /**
     * Create a new job instance.
     *
     * @param $service
     * @param array $options
     */
    public function __construct($service, array $options)
    {
        $this->service = $service;
        $this->options = $options;
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\ServerService | ServerServiceContract $serverService
     */
    public function handle(ServerService $serverService)
    {
        /** @var UserServer $userServer */
        $userServer = $serverService->createServer($this->service, $this->options);

        while($serverStatus = $serverService->getServerStatus($userServer) == 'new') {
            $serverStatus = $serverService->getServerStatus($userServer);
        }

        event(new ServerCreated($userServer));

        $serverService->saveServerInfo($userServer);

//        dispatch(new ProvisionServer($userServer));
    }
}
