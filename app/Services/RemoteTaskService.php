<?php

namespace App\Services;

use App\Contracts\RemoteTaskServiceContract;
use App\Exceptions\FailedCommand;
use App\Exceptions\SshConnectionFailed;
use App\Models\Server\Server;
use App\Models\Site\Site;
use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;

class RemoteTaskService implements RemoteTaskServiceContract
{
    private $user;
    private $server;
    /** @var SSH2 */
    private $session;
    private $errors = [];

    private $output = '';

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

        if ('local' === config('app.env')) {
            \Log::info('Running Command '.$command);
        }

        try {
            $output = $this->session->exec('source /etc/profile && '.rtrim($command, ';').' && echo codepier-done;');
        } catch (\ErrorException $e) {
            if ('Unable to open channel' == $e->getMessage()) {
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

        $output = $this->cleanOutput($output);

        if ('local' === config('app.env')) {
            \Log::info($output);
        }

        if (! empty($output)) {
            $this->output .= $output."\n";
        }

        if (0 != $this->session->getExitStatus()) {
            \Log::critical('Error while running Command '.$command);
            \Log::critical($output);

            $this->errors[] = $output;

            throw new FailedCommand($output);
        }

        return $output;
    }

    /**
     * @param $file
     * @param $string
     *
     * @throws FailedCommand
     * @throws SshConnectionFailed
     * @throws \Exception
     *
     * @return bool
     */
    public function doesFileHaveLine($file, $string)
    {
        return filter_var($this->run("grep -R \"$string\" \"$file\" | wc -l"), FILTER_VALIDATE_INT) >= 1;
    }

    /**
     * @param $file
     * @param $string
     *
     * @throws FailedCommand
     * @throws SshConnectionFailed
     * @throws \Exception
     *
     * @return bool
     */
    public function getFileLine($file, $string)
    {
        return $this->run("grep -R \"$string\" \"$file\"");
    }

    /**
     * @param $file
     * @param $contents
     * @param bool $read
     *
     * @throws FailedCommand
     * @throws SshConnectionFailed
     * @throws \Exception
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
     * @throws FailedCommand
     * @throws SshConnectionFailed
     * @throws \Exception
     *
     * @return string
     */
    public function appendTextToFile($file, $text)
    {
        return $this->run("echo '$text' >> $file");
    }

    /**
     * @param $file
     * @param $findText
     * @param $text
     *
     * @throws FailedCommand
     * @throws SshConnectionFailed
     * @throws \Exception
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
     * @throws FailedCommand
     * @throws SshConnectionFailed
     * @throws \Exception
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
     * @throws FailedCommand
     * @throws SshConnectionFailed
     * @throws \Exception
     *
     * @return string
     */
    public function makeDirectory($directory)
    {
        if (! $this->hasDirectory($directory)) {
            return $this->run("mkdir -p $directory");
        }
    }

    /**
     * @param $directory
     *
     * @throws FailedCommand
     * @throws SshConnectionFailed
     * @throws \Exception
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
     * @throws FailedCommand
     * @throws SshConnectionFailed
     * @throws \Exception
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
     * @param $double
     * @param $delim
     *
     * @throws FailedCommand
     * @throws SshConnectionFailed
     * @throws \Exception
     *
     * @return string
     */
    public function updateText($file, $text, $replaceWithText, $double = false, $delim = '/')
    {
        if (! $this->doesFileHaveLine($file, $text)) {
            \Log::critical($file.' does not contain'.$text);
        }

        if (true == $double) {
            return $this->run('sed -i "s'.$delim.'.*'.$text.'.*'.$delim.$replaceWithText.$delim.'" '.$file);
        }

        $text = $this->cleanRegex($text);
        $replaceWithText = $this->cleanText($replaceWithText);

        return $this->run("sed -i 's$delim.*$text.*$delim$replaceWithText$delim' $file");
    }

    /**
     * Checks to see if the server has the file.
     *
     * @param $file
     *
     * @throws FailedCommand
     * @throws SshConnectionFailed
     * @throws \Exception
     *
     * @return bool
     */
    public function hasFile($file)
    {
        return filter_var($this->run("[ -f $file ] && echo true || echo false"), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @param $file
     *
     * @throws FailedCommand
     * @throws SshConnectionFailed
     * @throws \Exception
     *
     * @return bool
     */
    public function isFileEmpty($file)
    {
        if ($this->hasFile($file)) {
            return 0 == filter_var($this->run("cat $file | wc -c"), FILTER_VALIDATE_INT);
        }

        return true;
    }

    /**
     * @param $file
     *
     * @throws FailedCommand
     * @throws SshConnectionFailed
     * @throws \Exception
     *
     * @return string
     */
    public function getFileContents($file)
    {
        return $this->run("cat $file");
    }

    /**
     * Checks to see if the server has the file.
     *
     * @param $directory
     *
     * @throws FailedCommand
     * @throws SshConnectionFailed
     * @throws \Exception
     *
     * @return bool
     */
    public function hasDirectory($directory)
    {
        return filter_var($this->run("[ -d $directory ] && echo true || echo false"), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @param \App\Models\Server\Server $server
     * @param string                    $user
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

        $ssh = new SSH2($this->server->ip, $this->server->port);

        try {
            // TODO - login as codepier / sudo to root
            if (! $ssh->login($user, $key)) {
                $server->ssh_connection = false;
                $server->save();

                throw new SshConnectionFailed('We are unable to connect to your server '.$this->server->name.' ('.$this->server->ip.').');
            }
        } catch (\Exception $e) {
            $server->update([
                'ssh_connection' => false,
            ]);

            throw new SshConnectionFailed($e->getMessage());
        }

        if (! $server->ssh_connection) {
            $server->update([
                'ssh_connection' => true,
            ]);
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
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    public function clearOutput()
    {
        return $this->output = '';
    }

    /**
     * @param $response
     *
     * @return string
     */
    private function cleanOutput($response)
    {
        return trim(preg_replace('/codepier-done|Wrote/', '', $response));
    }

    /**
     * http://unix.stackexchange.com/questions/32907/what-characters-do-i-need-to-escape-when-using-sed-in-a-sh-script.
     *
     * @param $text
     *
     * @return mixed
     */
    private function cleanText($text)
    {
        $text = preg_replace('#(&|\\\|\/)#', '\\\$1', $text);
        $text = str_replace("'", "'\\''", $text);

        return $text;
    }

    /**
     * http://unix.stackexchange.com/questions/32907/what-characters-do-i-need-to-escape-when-using-sed-in-a-sh-script.
     *
     * @param $text
     *
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
     *
     * @return array
     */
    public function createSshKey()
    {
        $rsa = new RSA();
        $rsa->setPublicKeyFormat(RSA::PUBLIC_FORMAT_OPENSSH);

        return $rsa->createKey();
    }

    /**
     * @param Site   $site
     * @param Server $server
     *
     * @throws FailedCommand
     * @throws SshConnectionFailed
     * @throws \Exception
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
