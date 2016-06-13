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
        $live = true;

        $process = new Process('eval `ssh-agent -s` && echo kashani1 | ssh-add &&  ~/.composer/vendor/bin/envoy run provision --server='.$userServer->ip.' --branch=master --path=/home/codepier/laravel');
        $process->setTimeout(3600);
        $process->setIdleTimeout(300);
        $process->setWorkingDirectory(base_path());

        try {
            $process->run(function ($type, $buffer) use ($live, &$result) {
                if ($live) {
                    \Log::info($buffer);
                    echo $buffer . '</br />';
                }

                $result[] = $buffer;
            });
        } catch (ProcessFailedException $e) {
            dd($e->getMessage());
        }

        return $result;
    }
}