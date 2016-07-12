<?php

namespace App\Jobs;

use App\Contracts\Server\ServerServiceContract;
use App\Contracts\Server\ServerServiceContract as ServerService;
use App\Events\Server\ServerCreated;
use App\Models\Server;
use App\Models\ServerProvider;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class CreateServer
 * @package App\Jobs
 */
class CreateServer extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, DispatchesJobs;

    protected $user;
    protected $name;
    protected $options;
    protected $serverProvider;

    /**
     * Create a new job instance.
     * @param ServerProvider $serverProvider
     * @param User $user
     * @param $name
     * @param array $options
     */
    public function __construct(ServerProvider $serverProvider, User $user, $name, array $options)
    {
        $this->user = $user;
        $this->name = $name;
        $this->options = $options;
        $this->serverProvider = $serverProvider;
    }

    /**
     * Execute the job.
     * @param \App\Services\Server\ServerService | ServerServiceContract $serverService
     * @throws \Exception
     */
    public function handle(ServerService $serverService)
    {
        \Log::info('Creating Server');
        /** @var Server $server */
        $server = $serverService->create($this->serverProvider, $this->user, $this->name, $this->options);
        \Log::info('Created Server');

        $serverStatus = 'new';

        \Log::info('Server Status');
        while ($serverStatus == 'new') {
            sleep(5);
            $serverStatus = $serverService->getStatus($server);
            \Log::info('Server Status ' .$serverStatus);
        }
        event(new ServerCreated($server));

        $serverService->saveInfo($server);

        $sshConnection = false;


        while ($sshConnection == false) {
            sleep(5);
            $sshConnection = $serverService->testSshConnection($server);
            \Log::info('SSH Status ' .$sshConnection);
        }
        
        \Log::info('Finished Creating Server' .$sshConnection);
        dispatch(new ProvisionServer($server));
//            ->onQueue('server_provision'));
    }
}
