<?php

namespace App\Services\Server\Site;

use App\Contracts\Server\ServerServiceContract;
use App\Contracts\Server\Site\SiteServiceContract;
use App\Models\Server;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Class SiteService
 * @package App\Services
 */
class SiteService implements SiteServiceContract
{
    public function create(Server $server, $domain = 'default')
    {
        $live = true;

        $process = new Process('eval `ssh-agent -s` && echo kashani1 | ssh-add &&  ~/.composer/vendor/bin/envoy run create_site --user=root --server='.$server->ip.' --domain='.$domain);
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

    public function deploy(Server $server)
    {
        $live = true;

        $process = new Process('eval `ssh-agent -s` && echo kashani1 | ssh-add &&  ~/.composer/vendor/bin/envoy run deploy --user=codepier --server='.$server->ip.' --branch=master --path=/home/codepier/default');
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