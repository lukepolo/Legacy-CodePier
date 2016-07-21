<?php

namespace App\Services;

use App\Contracts\RemoteTaskServiceContract;
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
    private $server;
    private $session;
    private $errors = [];

    /**
     * Runs a command on a remote server
     *
     * @param $command
     * @param bool $read
     * @param bool $expectedFailure
     * @return bool
     * @throws SshConnectionFailed
     */
    public function run($command, $read = false, $expectedFailure = false)
    {
        if (!$this->server) {
            throw new SshConnectionFailed('No server set');
        }

        \Log::info('Running Command : ' . $command);

        try {
            $this->session->exec($command . "; echo 'done';");
        } catch (\ErrorException $e) {
            if ($e->getMessage() == "Unable to open channel") {
                \Log::warning('retrying to connect to');
                $this->ssh($this->server);
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
            if (!str_contains($error, 'WARN') && !str_contains($error, 'Warning') && !str_contains($error, 'Generating DH parameters')) {
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

    public function writeToFile($file, $contents, $read = false)
    {
        return $this->run('
cat > '.$file.' <<    \'EOF\'
'.trim($contents).'
EOF
echo "Wrote" ', $read);

    }

    public function appendTextToFile($file, $text)
    {
        return $this->run("echo $text >> $file");
    }

    public function findTextAndAppend($file, $findText, $text)
    {
        return $this->run("sed -i '/$findText/a $text' ".$file);
    }

    public function removeLineByText($file, $text)
    {
        $text = str_replace('/', '\/', $text);
        return $this->run("sed -i '/$text/d' ".$file);
    }

    public function makeDirectory($directory)
    {
        return $this->run("mkdir -p $directory");
    }

    public function removeDirectory($directory)
    {
        return $this->run("rm $directory -rf");
    }

    public function removeFile($file)
    {
        return $this->run("rm $file");
    }

    /**
     * Sets up the SSH connections
     *
     * @param Server $server
     * @param string $user
     * @throws \Exception
     */
    public function ssh(Server $server, $user = 'root')
    {
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
    }

    /**
     * Gets the errors from the current sessions
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}