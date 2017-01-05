<?php

namespace App\Services\Server;

use App\Models\SshKey;
use App\Models\CronJob;
use phpseclib\Net\SFTP;
use phpseclib\Crypt\RSA;
use App\Classes\DiskSpace;
use App\Models\Server\Server;
use App\Models\SslCertificate;
use App\Exceptions\SshConnectionFailed;
use App\Services\Systems\SystemService;
use App\Models\Server\Provider\ServerProvider;
use App\Contracts\Server\ServerServiceContract;
use App\Notifications\Server\ServerProvisioned;
use App\Contracts\Systems\SystemServiceContract;
use App\Events\Server\ServerSshConnectionFailed;
use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;

class ServerService implements ServerServiceContract
{
    protected $systemService;
    protected $remoteTaskService;

    public static $serverOperatingSystem = 'ubuntu-16-04-x64';

    const SSL_FILES = '/opt/codepier/ssl';
    const LETS_ENCRYPT = 'Let\'s Encrypt';

    /**
     * SiteService constructor.
     *
     * @param \App\Services\RemoteTaskService | RemoteTaskService $remoteTaskService
     * @param SystemService | SystemServiceContract $systemService
     */
    public function __construct(RemoteTaskService $remoteTaskService, SystemServiceContract $systemService)
    {
        $this->remoteTaskService = $remoteTaskService;
        $this->systemService = $systemService;
    }

    /**
     * @param ServerProvider $serverProvider
     * @param Server $server
     *
     * @return mixed
     */
    public function create(ServerProvider $serverProvider, Server $server)
    {
        $sshKey = $this->remoteTaskService->createSshKey();

        $server->public_ssh_key = $sshKey['publickey'];
        $server->private_ssh_key = $sshKey['privatekey'];

        $server->save();

        return $this->getProvider($serverProvider)->create($server);
    }

    /**
     * @param ServerProvider $serverProvider
     *
     * @return mixed
     */
    public function getServerOptions(ServerProvider $serverProvider)
    {
        return $this->getProvider($serverProvider)->getOptions();
    }

    /**
     * @param ServerProvider $serverProvider
     *
     * @return mixed
     */
    public function getServerRegions(ServerProvider $serverProvider)
    {
        return $this->getProvider($serverProvider)->getRegions();
    }

    /**
     * @param Server $server
     * @param string $user
     *
     * @return bool
     */
    public function testSSHConnection(Server $server, $user = 'root')
    {
        try {
            $this->remoteTaskService->ssh($server, $user);

            $server->ssh_connection = true;
            $server->save();

            return true;
        } catch (SshConnectionFailed $e) {
            event(new ServerSshConnectionFailed($server, $e->getMessage()));

            return false;
        }
    }

    /**
     * @param Server $server
     *
     * @param bool $noDelete
     * @return mixed
     */
    public function getStatus(Server $server, $noDelete = false)
    {
        $server->touch();

        try {
            $status = $this->getProvider($server->serverProvider)->getStatus($server);
            $server->status = $status;
            $server->save();

            return $status;
        } catch (\Exception $e) {
            if (! $noDelete && $e->getMessage() == 'The resource you were accessing could not be found.') {
                $server->delete();

                return 'Server Has Been Deleted';
            }
        }
    }

    /**
     * @param Server $server
     *
     * @return mixed
     */
    public function saveInfo(Server $server)
    {
        return $this->getProvider($server->serverProvider)->savePublicIP($server);
    }

    /**
     * @param Server $server
     *
     * @return bool
     */
    public function provision(Server $server)
    {
        if (empty($server->database_password)) {
            $server->database_password = str_random(32);
        }

        if (empty($server->sudo_password)) {
            $server->sudo_password = str_random(32);
        }

        $server->save();

        if (! $this->systemService->provision($server)) {
            return false;
        }

        $server->notify(new ServerProvisioned());

        return true;
    }

    /**
     * @param Server $server
     * @param string $user
     *
     * @return bool
     */
    public function restartServer(Server $server, $user = 'root')
    {
        $this->remoteTaskService->ssh($server, $user);

        $this->remoteTaskService->run('reboot now', false, true);

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param Server $server
     *
     * @return array
     */
    public function restartWebServices(Server $server)
    {
        $this->getService(SystemService::WEB, $server)->restartWebServices();
    }

    /**
     * @param Server $server
     *
     * @return bool
     */
    public function restartDatabase(Server $server)
    {
        $this->getService(SystemService::DATABASE, $server)->restartDatabase();
    }

    /**
     * @param Server $server
     *
     * @return bool
     */
    public function restartWorkers(Server $server)
    {
        $this->getService(SystemService::WORKERS, $server)->restartWorkers();
    }

    /**
     * @param Server $server
     * @param $file
     * @param $content
     * @param string $user
     *
     * @return bool
     */
    public function saveFile(Server $server, $file, $content, $user = 'root')
    {
        $this->remoteTaskService->ssh($server, $user);

        $this->remoteTaskService->writeToFile($file, str_replace("\r\n", PHP_EOL, $content));

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param Server $server
     * @param $filePath
     * @param string $user
     *
     * @return null|string
     */
    public function getFile(Server $server, $filePath, $user = 'root')
    {
        $key = new RSA();
        $key->loadKey($server->private_ssh_key);

        $ssh = new SFTP($server->ip);

        if (! $ssh->login($user, $key)) {
            exit('Login Failed');
        }

        if ($contents = $ssh->get($filePath)) {
            return trim($contents);
        }
    }

    /**
     * @param Server $server
     * @param $folder
     * @param string $user
     *
     * @return bool
     */
    public function createFolder(Server $server, $folder, $user = 'root')
    {
        $this->remoteTaskService->ssh($server, $user);

        $this->remoteTaskService->makeDirectory($folder);

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param Server $server
     * @param $folder
     * @param string $user
     *
     * @return bool
     */
    public function removeFolder(Server $server, $folder, $user = 'root')
    {
        $this->remoteTaskService->ssh($server, $user);

        $this->remoteTaskService->removeDirectory($folder);

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param Server $server
     * @param $sshKey
     *
     * @return bool
     */
    public function installSshKey(Server $server, SshKey $sshKey)
    {
        $this->remoteTaskService->ssh($server, 'codepier');

        $this->remoteTaskService->appendTextToFile('/home/codepier/.ssh/authorized_keys', $sshKey->ssh_key);

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param Server $server
     * @param $sshKey
     *
     * @return bool
     */
    public function removeSshKey(Server $server, SshKey $sshKey)
    {
        $this->remoteTaskService->ssh($server, 'codepier');

        $this->remoteTaskService->removeLineByText('/home/codepier/.ssh/authorized_keys', $sshKey->ssh_key);

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param Server $server
     *
     * @return DiskSpace
     */
    public function checkDiskSpace(Server $server)
    {
        $this->remoteTaskService->ssh($server);
        $results = $this->remoteTaskService->run("df / | grep / | awk '{print $2} {print $3} {print $4}'");

        $results = array_filter(explode(PHP_EOL, $results));

        return new DiskSpace($results[0], $results[1], $results[2]);
    }

    /**
     * @param Server $server
     * @param CronJob $cronJob
     * @return array
     */
    public function installCron(Server $server, CronJob $cronJob)
    {
        $this->remoteTaskService->ssh($server, $cronJob->user);
        $this->remoteTaskService->run('crontab -l | (grep '.$cronJob->job.') || ((crontab -l; echo "'.$cronJob->job.' >/dev/null 2>&1") | crontab)');

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param Server $server
     * @param CronJob $cronJob
     * @return bool
     */
    public function removeCron(Server $server, CronJob $cronJob)
    {
        $this->remoteTaskService->ssh($server, $cronJob->user);

        $job = str_replace('*', '\\*', $cronJob->job);
        $this->remoteTaskService->run("crontab -l | grep -v '$job' | crontab -");

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param Server $server
     * @param SslCertificate $sslCertificate
     * @return array
     */
    public function installSslCertificate(Server $server, SslCertificate $sslCertificate)
    {
        switch ($sslCertificate->type) {
            case self::LETS_ENCRYPT:

                return $this->installLetsEncryptSsl($server, $sslCertificate);

                break;
            case 'existing':

                $this->remoteTaskService->ssh($server);

                $sslCertPath = self::SSL_FILES.'/'.$sslCertificate->id;

                $sslCertificate->update([
                    'key_path' => $sslCertPath.'/server.key',
                    'cert_path' =>  $sslCertPath.'/server.crt',
                ]);

                $this->remoteTaskService->writeToFile($sslCertificate->key_path, $sslCertificate->key);
                $this->remoteTaskService->writeToFile($sslCertificate->cert_path, $sslCertificate->cert);

                return $this->remoteTaskService->getErrors();

                break;
        }
    }

    /**
     * @param Server $server
     * @param SslCertificate $sslCertificate
     * @return array
     */
    public function activateSslCertificate(Server $server, SslCertificate $sslCertificate)
    {
        $this->remoteTaskService->ssh($server);

        $sslCertPath = self::SSL_FILES.'/'.$sslCertificate->id;

        $this->remoteTaskService->makeDirectory($sslCertPath);

        $this->remoteTaskService->run("ln -f -s $sslCertificate->key_path $sslCertPath/server.key");
        $this->remoteTaskService->run("ln -f -s $sslCertificate->cert_path $sslCertPath/server.crt");

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param Server $server
     * @param SslCertificate $sslCertificate
     * @return array
     */
    public function removeSslCertificate(Server $server, SslCertificate $sslCertificate)
    {
        $this->remoteTaskService->ssh($server);

        switch ($sslCertificate->type) {
            case self::LETS_ENCRYPT:
                // leave it be we don't want to erase them cause they aren't unique
                break;
            default:
                $this->remoteTaskService->removeFile($sslCertificate->key_path);
                $this->remoteTaskService->removeFile($sslCertificate->cert_path);
                break;
        }

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param Server $server
     * @param SslCertificate $sslCertificate
     * @return array
     */
    private function installLetsEncryptSsl(Server $server, SslCertificate $sslCertificate)
    {
        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->run(
            'letsencrypt certonly --non-interactive --agree-tos --email '.$server->user->email.' --webroot -w /home/codepier/ --expand -d '.implode(' -d',
                explode(',', $sslCertificate->domains))
        );

        if (! count($this->remoteTaskService->getErrors())) {
            if (! $server->cronJobs
                ->where('job', '* */12 * * * letsencrypt renew')
                ->count()
            ) {
                $cronJob = CronJob::create([
                    'job' => '* */12 * * * letsencrypt renew',
                    'user' => 'root',
                ]);

                if (! count($this->installCron($server, $cronJob))) {
                    $server->cronJobs()->save($cronJob);
                }
            }
        }

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param ServerProvider $serverProvider
     *
     * @return mixed
     */
    private function getProvider(ServerProvider $serverProvider)
    {
        return new $serverProvider->provider_class($serverProvider->provider_name);
    }

    /**
     * @param $service
     * @param Server $server
     * @return mixed
     */
    public function getService($service, Server $server)
    {
        return $this->systemService->createSystemService($service, $server);
    }
}
