<?php

namespace App\Services;

use phpseclib\Net\SSH2;
use phpseclib\Crypt\RSA;
use App\Models\Server\Server;
use App\Exceptions\FailedCommand;
use App\Exceptions\SshConnectionFailed;
use App\Contracts\RemoteTaskServiceContract;

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
     *
     * @throws FailedCommand
     * @throws SshConnectionFailed
     * @throws \Exception
     *
     * @return array
     */
    public function run($command, $read = false, $expectedFailure = false)
    {
        if (! $this->server) {
            throw new SshConnectionFailed('No server set');
        }

        \Log::info('Running Command : '.$command);

        $output = null;

        try {
            $output = $this->session->exec('('.rtrim($command, ';').') && echo codepier-done;');
            if (! str_contains($output, 'codepier-done')) {
                \Log::info($output);
                $this->output[] = $output;
            }
        } catch (\ErrorException $e) {
            if ($e->getMessage() == 'Unable to open channel') {
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

        $output = $this->cleanResponse($output);

        if (! empty($output)) {
            $this->output[] = $output;
        }

        if ($this->session->getExitStatus() != 0) {
            \Log::error($output);

            $this->errors[] = $output;

            throw new FailedCommand($output);
        }

        return $output;
    }

    /**
     * @param $file
     * @param $contents
     * @param bool $read
     *
     * @return bool
     */
    public function writeToFile($file, $contents, $read = false)
    {
        $this->makeDirectory(preg_replace('#\/[^/]*$#', '', $file));

        return $this->run('
cat > '.$file.' <<    \'EOF\'
'.trim($contents).'
EOF
echo "Wrote" ', $read);
    }

    /**
     * @param $file
     * @param $text
     *
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
     *
     * @return bool
     */
    public function findTextAndAppend($file, $findText, $text)
    {
        return $this->run("sed -i '/$findText/a $text' ".$file);
    }

    /**
     * @param $file
     * @param $text
     *
     * @return bool
     */
    public function removeLineByText($file, $text)
    {
        $text = str_replace('/', '\/', $text);

        return $this->run("sed -i '/$text/d' ".$file);
    }

    /**
     * @param $directory
     *
     * @return bool
     */
    public function makeDirectory($directory)
    {
        return $this->run("mkdir -p $directory");
    }

    /**
     * @param $directory
     *
     * @return bool
     */
    public function removeDirectory($directory)
    {
        return $this->run("rm $directory -rf");
    }

    /**
     * @param $file
     *
     * @return bool
     */
    public function removeFile($file)
    {
        return $this->run("rm $file -f");
    }

    public function updateText($file, $text, $replaceWithText)
    {
        return $this->run('sed -i "s/'.$text.' .*/'.$replaceWithText.'/" '.$file);
    }

    /**
     * @param \App\Models\Server\Server $server
     * @param string $user
     *
     * @throws SshConnectionFailed
     *
     * @return bool
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
            if (! $ssh->login($user, $key)) {
                $server->ssh_connection = false;
                $server->save();

                throw new SshConnectionFailed('We are unable to connect to your server '.$this->server->name. ' ('.$this->server->ip.').');
            }
        } catch (\Exception $e) {
            $server->ssh_connection = false;
            $server->save();
            throw new SshConnectionFailed($e->getMessage());
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

    public function getOutput()
    {
        return array_filter($this->output);
    }

    private function cleanResponse($response)
    {
        return trim(str_replace('codepier-done', '', $response));
    }
}
