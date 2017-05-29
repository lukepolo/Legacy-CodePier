<?php

namespace App\Services;


use App\Contracts\RemoteTaskServiceContract;
use App\Contracts\SshContract;
use App\Events\SshLoginAttempted;
use App\Exceptions\FailedCommandException;
use App\Exceptions\SshConnectionFailed;
use Dompdf\Exception;
use Illuminate\Database\Eloquent\Collection;
use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;

class RemoteTaskService implements RemoteTaskServiceContract
{
    protected $ssh;

    protected $rsa;

    protected $server;

    protected $user = 'root';

    /**
     * SshClient constructor.
     * @param RSA $rsa
     */
    public function __construct( RSA $rsa)
    {
        $this->rsa = $rsa;
    }

    protected function getServer()
    {
        return $this->server ?? new SshConnectionFailed('No server set');
    }

    public function withServer(Collection $server) : self
    {
        $this->server = $server;
        $this->connect($server);
        return $this;
    }

    /**
     * @param array|string $commands
     * @param bool $read
     * @param bool $expectedFailure
     * @return mixed|static
     */
    public function run($commands, bool $read = false, bool $expectedFailure = false)
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
     * Checks to see if the server has the file.
     * @param $directory
     * @return string
     */
    public function hasDirectory($directory)
    {
        return filter_var($this->run("[ -d $directory ] && echo true || echo false"), FILTER_VALIDATE_BOOLEAN);
    }

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
     * @return mixed
     */
    private function cleanRegex($text)
    {
        $text = str_replace("'", "'\\''", $text);

        $text = preg_replace('#(\$|\.|\*|\/|\[|\\\|\]|\^)#', '\\\$1', $text);

        return $text;
    }
}