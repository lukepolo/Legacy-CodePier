<?php

namespace App\Services;

use App\Contracts\RemoteTaskServiceContract;
use App\Exceptions\FailedCommand;
use App\Exceptions\SshConnectionFailed;
use App\Models\Server;
use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;

/**
 * Class RemoteTaskService
 * @package App\Services
 */
class RemoteTaskService implements RemoteTaskServiceContract
{
    public $throwErrors;

    private $user;
    private $server;
    private $session;
    private $errors = [];

    private $okErrors = [
        'WARN',
        'Warning',
        'Generating DH parameters',
        'Identity added'
    ];

    /**
     * @param $command
     * @param bool $read
     * @param bool $expectedFailure
     * @return bool
     * @throws FailedCommand
     * @throws SshConnectionFailed
     */
    public function run($command, $read = false, $expectedFailure = false)
    {
        if (!$this->server) {
            throw new SshConnectionFailed('No server set');
        }

        \Log::info('Running Command : ' . $command);

        try {
            \Log::info($this->session->exec($command . "; echo 'done';"));
        } catch (\ErrorException $e) {
            if ($e->getMessage() == "Unable to open channel") {
                \Log::warning('retrying to connect to');
                $this->ssh($this->server, $this->user);
                $this->run($command, $read);
            } else {
                if($expectedFailure) {
                    set_error_handler(function($num, $str, $file, $line) {
                        return true;
                    });
                }

                \Log::critical($e->getMessage());

                return false;
            }
        }

        if (!empty($error = $this->session->getStdError())) {
            if (!str_contains($error, $this->okErrors)) {

                if($this->throwErrors) {
                    throw new FailedCommand($error);
                }

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
     * @param $file
     * @param $contents
     * @param bool $read
     * @return bool
     */
    public function writeToFile($file, $contents, $read = false)
    {
        return $this->run('
cat > '.$file.' <<    \'EOF\'
'.trim($contents).'
EOF
echo "Wrote" ', $read);

    }

    /**
     * @param $file
     * @param $text
     * @return bool
     */
    public function appendTextToFile($file, $text)
    {
        return $this->run("echo $text >> $file");
    }

    /**
     * @param $file
     * @param $findText
     * @param $text
     * @return bool
     */
    public function findTextAndAppend($file, $findText, $text)
    {
        return $this->run("sed -i '/$findText/a $text' ".$file);
    }

    /**
     * @param $file
     * @param $text
     * @return bool
     */
    public function removeLineByText($file, $text)
    {
        $text = str_replace('/', '\/', $text);
        return $this->run("sed -i '/$text/d' ".$file);
    }

    /**
     * @param $directory
     * @return bool
     */
    public function makeDirectory($directory)
    {
        return $this->run("mkdir -p $directory");
    }

    /**
     * @param $directory
     * @return bool
     */
    public function removeDirectory($directory)
    {
        return $this->run("rm $directory -rf");
    }

    /**
     * @param $file
     * @return bool
     */
    public function removeFile($file)
    {
        return $this->run("rm $file");
    }

    /**
     * @param Server $server
     * @param string $user
     * @return bool
     * @throws \Exception
     */
    public function ssh(Server $server, $user = 'root', $throwErrors = false)
    {
        $this->throwErrors = $throwErrors;

        // Check to see if we are already connected
        if($this->server == $server && $user == $this->user) {
            return true;
        }
        $this->user = $user;
        $this->server = $server;

        $key = new RSA();
        $key->loadKey($server->private_ssh_key);

        $ssh = new SSH2($this->server->ip);

        $ssh->enableQuietMode();

        try {
            if (!$ssh->login($user, $key)) {
                $server->ssh_connection = false;
                $server->save();
                throw new SshConnectionFailed('Failed to login');
            }
        } catch (\Exception $e) {
            $server->ssh_connection = false;
            $server->save();
            throw new SshConnectionFailed('Failed to login');
        }

        $ssh->setTimeout(0);

        $this->session = $ssh;

        return true;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}