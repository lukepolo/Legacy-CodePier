<?php

namespace App\Services;

use App\Contracts\SshContract;
use App\Contracts\RemoteTaskServiceContract;
use App\Exceptions\SshConnectionFailed;
use App\Models\Server\Server;
use Illuminate\Database\Eloquent\Collection;

class RemoteTaskService implements RemoteTaskServiceContract
{
    protected $ssh;

    protected $server;

    protected $fileHandler;

    /**
     * RemoteTaskService constructor.
     * @param SshContract $ssh
     * @param Server $server
     */
    public function __construct(SshContract $ssh)
    {
        $this->ssh = $ssh;
    }

    public function run($commands, bool $read = false, $expectedFailure = false)
    {
        return $this->ssh->run($commands);
    }

    protected function getServer()
    {
        return $this->server ?? new SshConnectionFailed('No server set');
    }

    public function withServer(Collection $server) : self
    {
        $this->server = $server;
        $this->connectTo($server);
        return $this;
    }

    public function connectTo($server, $user = 'root'): void
    {
        $this->ssh->connect($server);
    }

}