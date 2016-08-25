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
    private $user;
    private $server;
    private $session;
    private $errors = [];

    private $output = [];

    /**
     * @param $command
     * @param bool $read
     * @param bool $expectedFailure
     * @return array
     * @throws FailedCommand
     * @throws SshConnectionFailed
     * @throws \Exception
     */
    public function run($command, $read = false, $expectedFailure = false)
    {
        if (!$this->server) {
            throw new SshConnectionFailed('No server set');
        }

        \Log::info('Running Command : ' . $command);

        $output = null;

        try {
            $output = $this->session->exec(rtrim($command, ';') . " && echo codepier-done;");
            if(!str_contains($output, 'codepier-done')) {
                \Log::info($output);
                $this->output[] = $output;
            }
        } catch (\ErrorException $e) {
            if ($e->getMessage() == "Unable to open channel") {
                \Log::warning('retrying to connect to');
                $this->ssh($this->server, $this->user);
                $this->run($command, $read);
            } else {
                if ($expectedFailure) {
                    set_error_handler(function ($num, $str, $file, $line) {
                        return true;
                    });
                }

                throw new SshConnectionFailed('We were unbale to connect to you server '.$this->server->name.'.');
            }
        }

        \Log::debug($this->session->getExitStatus());

        $this->output[] = $output;

        if ($this->session->getExitStatus() != 0) {

            \Log::error($output);

            $this->errors[] = $output;

            throw new FailedCommand(json_encode($output));
        }

        return $this->cleanResponse($output);
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
cat > ' . $file . ' <<    \'EOF\'
' . trim($contents) . '
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
        return $this->run("sed -i '/$findText/a $text' " . $file);
    }

    /**
     * @param $file
     * @param $text
     * @return bool
     */
    public function removeLineByText($file, $text)
    {
        $text = str_replace('/', '\/', $text);
        return $this->run("sed -i '/$text/d' " . $file);
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

    public function updateText($file, $text, $replaceWithText)
    {
        return $this->run('sed -i "s/'.$text.' .*/'.$replaceWithText.'/" '.$file);
    }

    /**
     * @param Server $server
     * @param string $user
     * @return bool
     * @throws SshConnectionFailed
     */
    public function ssh(Server $server, $user = 'root')
    {
        if ($this->server == $server && $user == $this->user) {
            return true;
        }

        $this->user = $user;
        $this->server = $server;

        $key = new RSA();
        $key->loadKey($server->private_ssh_key);

        $ssh = new SSH2($this->server->ip);

        try {
            if (!$ssh->login($user, $key)) {
                $server->ssh_connection = false;
                $server->save();

                throw new SshConnectionFailed('It seems your server ('.$this->server->name.') is offline.');
            }
        } catch (\Exception $e) {
            $server->ssh_connection = false;
            $server->save();
            throw new SshConnectionFailed('It seems your server ('.$this->server->name.') is offline.');
        }

        $ssh->setTimeout(2);

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

    public function getOutput()
    {
        return $this->output;
    }

    private function cleanResponse($response)
    {
        return str_replace('codepier-done', '', $response);
    }
}