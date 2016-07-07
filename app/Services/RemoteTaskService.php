<?php

namespace App\Services;

use App\Contracts\RemoteTaskServiceContract;
use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;
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
     *
     * @return bool
     */
    public function run($ip)
    {
        $live = true;

        $process = $this->ssh($ip);


        dd($process->getOutput());
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

    public function ssh($ip)
    {

        $key = new RSA();
        $key->setPassword(env('SSH_KEY_PASSWORD'));
        $key->loadKey(file_get_contents('/home/vagrant/.ssh/id_rsa'));

        $ssh = new SSH2($ip);

        if (!$ssh->login('root', $key)) {
            exit('Login Failed');
        }


        dd($ssh->read());

        dd('done.');

//
//        $process = new Process('
//            eval `ssh-agent -s` &&
//            echo ' . env('SSH_KEY_PASSWORD') . ' | ssh-add &&
//            ssh-keygen -R ' . $ip . ' &&
//        ');
//
//        $process->setTimeout(3600);
//        $process->setIdleTimeout(300);
//
//        return $process;
    }
}