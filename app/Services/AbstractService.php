<?php

namespace App\Services;


use App\Contracts\RemoteTaskServiceContract;
use App\Models\Server\Server;
use Illuminate\Database\Eloquent\Collection;

abstract class AbstractService
{
    protected $remoteTaskService;

    protected $server;

    /**
     * AbstractService constructor.
     * @param RemoteTaskServiceContract $remoteTaskService
     * @param Server $server
     * @internal param $service
     */
    public function __construct(RemoteTaskServiceContract $remoteTaskService, Collection $server)
    {
        $this->remoteTaskService = $remoteTaskService;
        $this->server = $server;
    }

    public function connectToServer($user = 'root'): void
    {
        $this->remoteTaskService->connect($this->server);
    }

    public function addToServiceRestartGroup($group, string $command): void
    {
        $serviceGroupFile = '/opt/codepier/'.$group;
        $this->remoteTaskService->appendTextToFile($serviceGroupFile, $command);

        $this->remoteTaskService->run('chmod 775 '.$serviceGroupFile);
    }

}