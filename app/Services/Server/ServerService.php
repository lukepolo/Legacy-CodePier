<?php

namespace App\Services\Server;

use App\Contracts\Server\ServerServiceContract;
use App\Models\UserServer;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Class ServerService
 * @package App\Services
 */
class ServerService implements ServerServiceContract
{
    public $services = [
        'digitalocean' => ServerProviders\DigitalOceanProvider::class
    ];

    private function getService($service)
    {
        return new $this->services[$service]();
    }

    public function createServer($service, array $options)
    {
        return $this->getService($service)->create();
    }

    public function getServerStatus(UserServer $userServer)
    {
        return $this->getService($userServer->service)->getStatus($userServer);
    }

    public function saveServerInfo(UserServer $userServer)
    {
        return $this->getService($userServer->service)->savePublicIP($userServer);
    }

    public function provision(UserServer $userServer)
    {
        $process = new Process('ssh root@104.131.10.102');
//        $process = new Process('~/.composer/vendor/bin/envoy run deploy --server=104.131.10.102 --branch=master --path=/home/codepier/laravel');
//        $process = new Process('whoami');
        $process->setTimeout(3600);
        $process->setIdleTimeout(300);
        $process->setWorkingDirectory(base_path());

        $process->run(function ($type, $buffer) {
            if (Process::ERR === $type) {
                echo 'ERR > '.$buffer;
            } else {
                echo 'OUT > '.$buffer;
            }
        });
    }
}