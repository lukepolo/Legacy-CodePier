<?php

namespace App\Services;


use App\Contracts\SshContract;
use App\Events\SshLoginAttempted;
use App\Exceptions\FailedCommandException;
use App\Exceptions\SshConnectionFailed;
use Dompdf\Exception;
use Illuminate\Database\Eloquent\Collection;
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

    public function run($commands, $read = false, $expectedFailure = false)
    {
        $ssh = $this->getSsh();
        $outputs = collect($commands)->map(function ($command) use ($ssh) {
            $output = $ssh->exec($this->processCommand($command));
            $this->checkOutputStatus($output);
            return $output;
        });
        return $outputs->count() != 1 ?  $outputs : $outputs->first();


    }

    protected function getSsh()
    {
        if (!$this->ssh) {
            throw new SshConnectionFailed('Not Connected');
        }
        return $this->ssh;
    }

    public function connect(Collection $server): void
    {
      $this->rsa->loadKey(data_get($server, 'private_ssh_key'));
        $this->setSsh(data_get($server, 'ip'), data_get($server, 'port'));
        $this->attemptLogin($server);
        $this->ssh->setTimeout(0);
    }

    protected function attemptLogin(Collection $server)
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

    public function checkOutputStatus($output) : void
    {
        if ($this->ssh->getExitStatus()) {
            throw new FailedCommandException($output);
        }
    }

    protected function setSsh($ip, $port)
    {
        $this->ssh = app()->makeWith(SSH2::class, $ip, $port);
    }
}