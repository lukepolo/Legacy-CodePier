<?php

namespace App\Services;

use App\Contracts\SshContract;
use App\Contracts\RemoteTaskServiceContract;
use App\Exceptions\SshConnectionFailed;
use App\Models\Server\Server;

class RemoteTaskService implements RemoteTaskServiceContract
{
    protected $ssh;

    protected $server;

    /**
     * RemoteTaskService constructor.
     * @param SshContract $ssh
     * @param Server $server
     */
    public function __construct(SshContract $ssh)
    {
        $this->ssh = $ssh;
    }

    public function run(array $commands, bool $read = false, $expectedFailure = false) {
        $server = $this->getServer();

        $this->ssh->run($commands);

    }

    protected function getServer()
    {
        return $this->server ?? new SshConnectionFailed('No server set');
    }

    public function withServer(Server $server) : self
    {
        $this->server = $server;
        $this->ssh->connect($server);
        return $this;
    }




}