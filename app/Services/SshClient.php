<?php

namespace App\Services;


use App\Contracts\SshContract;
use App\Exceptions\FailedCommandException;
use App\Providers\SshLoginAttempted;
use Dompdf\Exception;
use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;

class SshClient implements SshContract
{
    protected $ssh;

    protected $rsa;

    protected $user = 'root';

    /**
     * SshClient constructor.
     * @param RSA $rsa
     */
    public function __construct( RSA $rsa)
    {
        $this->rsa = $rsa;
    }

    public function run($commands, $read = false, $expectedFailure = false): string
    {
        if (is_array($commands)) {
            collect($commands)->each(function ($command) {
                $this->ssh->exec($this->processCommand($command));
            });

        }
        $output = $this->cleanOutput($this->ssh->exec($this->processCommand($commands)));

        $this->checkOutputStatus($output);

        return $output;
    }

    public function connect($server): void
    {
        $this->rsa->loadKey($server->private_ssh_key);
        $this->ssh = app()->makeWith(SSH2::class, [$server->ip, $server->port]);
        $this->attemptLogin($server);
        $this->ssh->setTimeout(0);
    }

    protected function attemptLogin($server)
    {
        try {
            $result = $this->ssh->login($this->user, $this->rsa);
            event(new SshLoginAttempted($server, $result));

        } catch (Exception $exception) {
            event(new SshLoginAttempted($server, false));
        }
    }

    public function for(string $user): sshContract
    {
        $this->user = $user;
        return $this;
    }

    public function createSsHKey()
    {
        $this->rsa->setPublicKeyFormat(RSA::PUBLIC_FORMAT_OPENSSH);
        return $this->rsa->createKey();
    }

    protected function processCommand(string $command) : string
    {
        return 'source /etc/profile && ' . $command . ' && echo codepier-done; ';
    }

    protected function cleanOutput($response) : string
    {
        return trim(str_replace('codepier-done', '', $response));
    }

    private function checkOutputStatus($output) : void
    {
        if ($this->ssh->getExitStatus()) {
            throw new FailedCommandException($output);
        }
    }
}