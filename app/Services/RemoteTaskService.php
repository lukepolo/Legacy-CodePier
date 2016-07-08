<?php

namespace App\Services;

use App\Contracts\RemoteTaskServiceContract;
use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;

/**
 * Class RemoteTaskService
 * @package App\Services
 */
class RemoteTaskService implements RemoteTaskServiceContract
{
    private $session;
    private $ip;

    /**
     * Runs a command on a remote server
     *
     * @param $command
     * @param bool $read
     * @return
     * @throws \Exception
     */
    public function run($command, $read = false)
    {
        try {
            $results = $this->session->exec($command.";");
        } catch(\ErrorException $e) {

            if($e->getMessage() == 'Unable to open channel') {
                $this->ssh($this->ip);
                $this->run($command, $read);
            }
            throw new \Exception($e->getMessage());
        }

        if(!empty($error = $this->session->getStdError())) {
            \Log::error($error);
        }

        if(!empty($results)) {
            \Log::info($results);
        }

        if($read) {
            return $this->session->read();
        }
    }

    public function ssh($ip)
    {
        $this->ip = $ip;

        $key = new RSA();
        $key->setPassword(env('SSH_KEY_PASSWORD'));
        $key->loadKey(file_get_contents('/home/vagrant/.ssh/id_rsa'));

        $ssh = new SSH2($ip);

        $ssh->enableQuietMode();

        if (!$ssh->login('root', $key)) {
            exit('Login Failed');
        }

        $this->session = $ssh;
    }
}