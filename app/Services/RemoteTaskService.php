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
    private $ip;
    private $errors = [];
    private $session;

    /**
     * Runs a command on a remote server
     *
     * @param $command
     * @param bool $read
     * @return bool
     * @throws \Exception
     */
    public function run($command, $read = false)
    {
        \Log::info('Running Command : ' . $command);

        try {
            $this->session->exec($command . "; echo 'done';");
        } catch (\ErrorException $e) {
            if ($e->getMessage() == "Unable to open channel") {
                \Log::warning('retrying to connect to');
                $this->ssh($this->ip);
                $this->run($command, $read);
            } else {
                dd($e->getMessage());
            }
        }

        if (!empty($error = $this->session->getStdError())) {
            if (!str_contains($error, 'WARN') && !str_contains($error, 'Warning')) {
                \Log::error($error);
                $this->errors[] = $error;
                return $error;
            }
        }

        if ($read) {
            return $this->session->read();
        }

        return true;
    }

    /**
     * Sets up the SSH connections
     *
     * @param $ip
     * @throws \Exception
     */
    public function ssh($ip, $user = 'root')
    {
        $this->ip = $ip;

        $key = new RSA();
        $key->setPassword(env('SSH_KEY_PASSWORD'));
        $key->loadKey(file_get_contents('/home/vagrant/.ssh/id_rsa'));

        $ssh = new SSH2($ip);

        $ssh->enableQuietMode();

        try {
            if (!$ssh->login($user, $key)) {
                throw new \Exception('Failed to login');
            }
        } catch (\Exception $e) {
            throw new \Exception('Failed to login');
        }

        $ssh->setTimeout(0);


        $this->session = $ssh;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}