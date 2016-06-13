<?php

namespace App\Jobs;

use App\Contracts\Server\ServerServiceContract;
use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Events\ServerCreated;
use App\Models\Server;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateServer extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, DispatchesJobs;

    protected $user;
    protected $service;
    protected $options;

    /**
     * Create a new job instance.
     *
     * @param $service
     * @param User $user
     * @param array $options
     */
    public function __construct($service, User $user, array $options)
    {
        $this->service = $service;
        $this->user = $user;
        $this->options = $options;
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\Server\ServerService | ServerServiceContract $serverService
     */
    public function handle(ServerService $serverService)
    {
        /** @var Server $server */
        $server = $serverService->create($this->service, $this->user, $this->options);

        while($serverStatus = $serverService->getStatus($server) == 'new') {
            $serverStatus = $serverService->getStatus($server);
        }

        event(new ServerCreated($server));

        $serverService->saveInfo($server);

        dispatch(new ProvisionServer($server));
    }
}
