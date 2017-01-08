<?php

namespace App\Services;

use phpseclib\Net\SSH2;
use phpseclib\Crypt\RSA;
use App\Models\Site\Site;
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
     * @return string
     */
    public function run($command, $read = false, $expectedFailure = false)
    {
        if (! $this->server) {
            throw new SshConnectionFailed('No server set');
        }

        $output = null;

        \Log::info('Running Command '.$command);

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
     * @return string
     */
    public function writeToFile($file, $contents, $read = false)
    {
        $this->makeDirectory(preg_replace('#\/[^/]*$#', '', $file));

        $contents = trim($contents);

        return $this->run("cat > $file << 'EOF'
$contents
EOF
echo \"Wrote\"", $read);
    }

    /**
     * @param $file
     * @param $text
     *
     * @return string
     */
    public function appendTextToFile($file, $text)
    {
        $text = str_replace('"', '\\"', $text);

        return $this->run("echo '$text' >> $file");
    }

    /**
     * @param $file
     * @param $findText
     * @param $text
     *
     * @return string
     */
    public function findTextAndAppend($file, $findText, $text)
    {
        $findText = $this->cleanRegex($findText);
        $text = $this->cleanText($text);

        return $this->run("sed -i '/$findText/a $text' $file");
    }

    /**
     * @param $file
     * @param $text
     *
     * @return string
     */
    public function removeLineByText($file, $text)
    {
        $text = $this->cleanRegex($text);

        return $this->run("sed -i '/$text/d' $file");
    }

    /**
     * @param $directory
     *
     * @return string
     */
    public function makeDirectory($directory)
    {
        return $this->run("mkdir -p $directory");
    }

    /**
     * @param $directory
     *
     * @return string
     */
    public function removeDirectory($directory)
    {
        return $this->run("rm $directory -rf");
    }

    /**
     * @param $file
     *
     * @return string
     */
    public function removeFile($file)
    {
        return $this->run("rm $file -f");
    }

    /**
     * @param $file
     * @param $text
     * @param $replaceWithText
     * @return string
     */
    public function updateText($file, $text, $replaceWithText)
    {
        $text = $this->cleanRegex($text);
        $replaceWithText = $this->cleanText($replaceWithText);

        return $this->run("sed -i 's/.*$text.*/$replaceWithText/' $file");
    }

    /**
     * Checks to see if the server has the file.
     * @param $file
     * @return string
     */
    public function hasFile($file)
    {
        return filter_var($this->run("[ -f $file ] && echo true || echo false"), FILTER_VALIDATE_BOOLEAN);
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

                throw new SshConnectionFailed('We are unable to connect to your server '.$this->server->name.' ('.$this->server->ip.').');
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

    /**
     * @return array
     */
    public function getOutput()
    {
        return array_filter($this->output);
    }

    /**
     * @param $response
     * @return string
     */
    private function cleanResponse($response)
    {
        return trim(str_replace('codepier-done', '', $response));
    }

    /**
     * http://unix.stackexchange.com/questions/32907/what-characters-do-i-need-to-escape-when-using-sed-in-a-sh-script.
     *
     * @param $text
     * @return mixed
     */
    private function cleanText($text)
    {
        $text = str_replace("'", "'\\''", $text);

        $text = preg_replace('#(&|\\\|\/)#', '\\\$1', $text);

        return $text;
    }

    /**
     * http://unix.stackexchange.com/questions/32907/what-characters-do-i-need-to-escape-when-using-sed-in-a-sh-script.
     *
     * @param $text
     * @return mixed
     */
    private function cleanRegex($text)
    {
        $text = str_replace("'", "'\\''", $text);

        $text = preg_replace('#(\$|\.|\*|\/|\[|\\\|\]|\^)#', '\\\$1', $text);

        return $text;
    }

    /**
     * Creates a new ssh key.
     * @return array
     */
    public function createSshKey()
    {
        $rsa = new RSA();
        $rsa->setPublicKeyFormat(RSA::PUBLIC_FORMAT_OPENSSH);

        return $rsa->createKey();
    }

    /**
     * @param Site $site
     * @param Server $server
     */
    public function saveSshKeyToServer(Site $site, Server $server)
    {
        if (! empty($site->public_ssh_key)) {
            $sshFile = '/home/codepier/.ssh/'.$site->id.'_id_rsa';

            $this->ssh($server, 'codepier');

            $this->writeToFile($sshFile, $site->private_ssh_key);
            $this->writeToFile($sshFile.'.pub', $site->public_ssh_key);

            $this->appendTextToFile('~/.ssh/config', "IdentityFile $sshFile");

            $this->run('chmod 600 /home/codepier/.ssh/* -R');
        }
    }
}
