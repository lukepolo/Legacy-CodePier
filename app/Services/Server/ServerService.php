<?php

namespace App\Services\Server;

use App\Contracts\Server\ServerServiceContract;
use App\Models\Server;
use App\Models\User;
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

    /**
     * @param $service
     * @return mixed
     */
    private function getService($service)
    {
        return new $this->services[$service]();
    }

    /**
     * @param $service
     * @param User $user
     * @param array $options
     * @return mixed
     */
    public function create($service, User $user, array $options)
    {
        return $this->getService($service)->create($user, $options);
    }

    /**
     * @param Server $server
     * @return mixed
     */
    public function getStatus(Server $server)
    {
        return $this->getService($server->service)->getStatus($server);
    }

    /**
     * @param Server $server
     * @return mixed
     */
    public function saveInfo(Server $server)
    {
        return $this->getService($server->service)->savePublicIP($server);
    }

    /**
     * @param Server $server
     * @return bool
     */
    public function provision(Server $server)
    {
        $live = true;

        $process = new Process('
            eval `ssh-agent -s` && 
            echo kashani1 | ssh-add &&
             ~/.composer/vendor/bin/envoy run provision --user=root --server='.$server->ip.' --branch=master --path=/home/codepier/laravel
        ');

        $process->setTimeout(3600);
        $process->setIdleTimeout(300);
        $process->setWorkingDirectory(base_path());

        $result = [];

        try {
            $process->run(function ($type, $buffer) use ($live, &$result) {
                if ($live) {
                    \Log::info($buffer);
                    echo $buffer . '</br />';
                }

                $result[] = $buffer;
            });
        } catch (ProcessFailedException $e) {
            return false;
        }

        return true;
    }
}