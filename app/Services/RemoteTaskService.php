<?php

namespace App\Services;

use App\Contracts\RemoteTaskServiceContract;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Class RemoteTaskService
 * @package App\Services
 */
class RemoteTaskService implements RemoteTaskServiceContract
{
    /**
     * Runs a command on a remote server
     *
     * @param $ip
     * @param $user
     * @param $task
     * @param array $options
     *
     * @return bool
     */
    public function run($ip, $user, $task, array $options = [])
    {
        $live = true;

        $options = implode(' ', array_map(
            function ($value, $key) {
                return '--'.$key.'='.$value;
            },
            $options,
            array_keys($options)
        ));

        $process = new Process('
            eval `ssh-agent -s` &&
            echo ' . env('SSH_KEY_PASSWORD') . ' | ssh-add &&
            ssh-keygen -R ' . $ip . ' &&
            ~/.composer/vendor/bin/envoy run ' . $task . ' --server=' . $ip . ' --user=' . $user . ' ' . $options
        );

        $process->setTimeout(3600);
        $process->setIdleTimeout(300);
        $process->setWorkingDirectory(base_path());

        $result = [];

        $process->start();

        try {
            $process->wait(function ($type, $buffer) use ($live, &$result) {
                if ($live) {
                    \Log::info($buffer);
                }
                $result[] = $buffer;
            });
        } catch (ProcessFailedException $e) {
            return false;
        }

        return $result;
    }
}