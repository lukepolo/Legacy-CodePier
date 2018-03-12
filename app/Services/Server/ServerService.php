<?php

namespace App\Services\Server;

use App\Models\Bitt;
use App\Models\Schema;
use App\Models\SshKey;
use App\Models\CronJob;
use phpseclib\Net\SFTP;
use phpseclib\Crypt\RSA;
use App\Classes\DiskSpace;
use App\Models\SchemaUser;
use App\Models\Server\Server;
use App\Models\SslCertificate;
use App\Models\LanguageSetting;
use App\Exceptions\FailedCommand;
use App\Models\EnvironmentVariable;
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
    const BITT_FILES = '/opt/codepier/bitts';

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
     * @return mixed
     */
    public function getServerProviderUser(ServerProvider $serverProvider)
    {
        return $this->getProvider($serverProvider)->getUser(\Auth::user());
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
            broadcast(new ServerSshConnectionFailed($server, $e->getMessage()));

            return false;
        }
    }

    /**
     * @param Server $server
     *
     * @param bool $noDelete
     * @return mixed
     * @throws \Exception
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
        if (! $this->systemService->provision($server)) {
            return false;
        }

        $server->notify(new ServerProvisioned());

        return true;
    }

    /**
     * @param Server $server
     * @param string $user
     * @throws FailedCommand
     * @throws SshConnectionFailed
     * @throws \Exception
     */
    public function restartServer(Server $server, $user = 'root')
    {
        $this->remoteTaskService->ssh($server, $user);

        $this->remoteTaskService->run('reboot now', true);
    }

    /**
     * @param Server $server
     */
    public function restartWebServices(Server $server)
    {
        $this->getService(SystemService::WEB, $server)->restartWebServices();
    }

    /**
     * @param Server $server
     *
     */
    public function restartDatabase(Server $server)
    {
        $this->getService(SystemService::DATABASE, $server)->restartDatabase();
    }

    /**
     * @param Server $server
     *
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
     * @return void
     * @throws FailedCommand
     * @throws SshConnectionFailed
     * @throws \Exception
     */
    public function saveFile(Server $server, $file, $content, $user = 'root')
    {
        $this->remoteTaskService->ssh($server, $user);

        $this->remoteTaskService->writeToFile($file, str_replace("\r\n", PHP_EOL, $content));
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
     * @throws FailedCommand
     * @throws SshConnectionFailed
     * @throws \Exception
     */
    public function createFolder(Server $server, $folder, $user = 'root')
    {
        $this->remoteTaskService->ssh($server, $user);

        $this->remoteTaskService->makeDirectory($folder);
    }

    /**
     * @param Server $server
     * @param $folder
     * @param string $user
     *
     * @return void
     * @throws FailedCommand
     * @throws SshConnectionFailed
     * @throws \Exception
     */
    public function removeFolder(Server $server, $folder, $user = 'root')
    {
        $this->remoteTaskService->ssh($server, $user);

        $this->remoteTaskService->removeDirectory($folder);
    }

    /**
     * @param Server $server
     * @param SshKey $sshKey
     * @throws FailedCommand
     * @throws SshConnectionFailed
     * @throws \Exception
     */
    public function installSshKey(Server $server, SshKey $sshKey)
    {
        $this->remoteTaskService->ssh($server, 'codepier');

        $this->remoteTaskService->appendTextToFile('/home/codepier/.ssh/authorized_keys', $sshKey->ssh_key);
    }

    /**
     * @param Server $server
     * @param SshKey $sshKey
     * @throws FailedCommand
     * @throws SshConnectionFailed
     * @throws \Exception
     */
    public function removeSshKey(Server $server, SshKey $sshKey)
    {
        $this->remoteTaskService->ssh($server, 'codepier');

        $this->remoteTaskService->removeLineByText('/home/codepier/.ssh/authorized_keys', $sshKey->ssh_key);
    }

    /**
     * @param Server $server
     *
     * @return DiskSpace
     * @throws FailedCommand
     * @throws SshConnectionFailed
     * @throws \Exception
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
     * @return void
     * @throws FailedCommand
     * @throws SshConnectionFailed
     * @throws \Exception
     */
    public function installCron(Server $server, CronJob $cronJob)
    {
        $this->remoteTaskService->ssh($server, $cronJob->user);
        $this->remoteTaskService->run('crontab -l | (grep ' . $cronJob->job . ') || ((crontab -l; echo "' . $cronJob->job . ' > /dev/null 2>&1") | crontab)');
    }

    /**
     * @param Server $server
     * @param CronJob $cronJob
     * @throws FailedCommand
     * @throws SshConnectionFailed
     * @throws \Exception
     */
    public function removeCron(Server $server, CronJob $cronJob)
    {
        $this->remoteTaskService->ssh($server, $cronJob->user);

        $job = str_replace('*', '\\*', $cronJob->job);
        $this->remoteTaskService->run("crontab -l | grep -v '$job' | crontab -");
    }

    /**
     * @param Server $server
     * @param SslCertificate $sslCertificate
     * @throws FailedCommand
     * @throws SshConnectionFailed
     * @throws \Exception
     */
    public function installSslCertificate(Server $server, SslCertificate $sslCertificate)
    {
        switch ($sslCertificate->type) {
            case self::LETS_ENCRYPT:

                $this->installLetsEncryptSsl($server, $sslCertificate);

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
                break;
        }

        $sslCertificate->update([
            'active' => true,
        ]);
    }

    /**
     * @param Server $server
     * @param SslCertificate $sslCertificate
     * @throws FailedCommand
     * @throws SshConnectionFailed
     * @throws \Exception
     */
    public function activateSslCertificate(Server $server, SslCertificate $sslCertificate)
    {
        $this->remoteTaskService->ssh($server);

        $sslCertPath = self::SSL_FILES.'/'.$sslCertificate->id;

        $this->remoteTaskService->makeDirectory($sslCertPath);

        $this->remoteTaskService->writeToFile("$sslCertPath/server.key", $sslCertificate->key);
        $this->remoteTaskService->writeToFile("$sslCertPath/server.crt", $sslCertificate->cert);
    }

    /**
     * @param Server $server
     * @param SslCertificate $sslCertificate
     * @throws FailedCommand
     * @throws SshConnectionFailed
     * @throws \Exception
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
    }

    /**
     * @param Server $server
     * @param SslCertificate $sslCertificate
     * @throws FailedCommand
     * @throws SshConnectionFailed
     * @throws \Exception
     */
    private function installLetsEncryptSsl(Server $server, SslCertificate $sslCertificate)
    {
        // TODO - if we already have the key and cert we dont need to run this,
        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->run(
            '/opt/codepier/./certbot-auto certonly --staging --non-interactive --agree-tos --email ' . $server->user->email . ' --rsa-key-size 4096 --webroot -w /home/codepier/ --expand -d ' . implode(' -d',
                explode(',', $sslCertificate->domains))
        );

        $letsEncryptJob = '0 12 * * * /opt/codepier/./lets_encrypt_renewals';

        if (! $server->cronJobs
            ->where('job', $letsEncryptJob)
            ->count()
        ) {
            $cronJob = CronJob::create([
                'job' => $letsEncryptJob,
                'user' => 'root',
            ]);

            try {
                $this->installCron($server, $cronJob);
                $server->cronJobs()->save($cronJob);
            } catch (FailedCommand $e) {
                $cronJob->delete();
                throw $e;
            }
        }

        $sslCertificate->key = $this->getFile($server, $sslCertificate->key_path);
        $sslCertificate->cert = $this->getFile($server, $sslCertificate->cert_path);
        $sslCertificate->save();
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

    /**
     * @param Server $server
     * @param Bitt $bitt
     * @return string
     * @throws FailedCommand
     * @throws SshConnectionFailed
     * @throws \Exception
     */
    public function runBitt(Server $server, Bitt $bitt)
    {
        $bitt->increment('uses');

        $bittFile = 'bitt-'.$bitt->id.'.sh';

        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->makeDirectory(self::BITT_FILES);
        $this->remoteTaskService->writeToFile(self::BITT_FILES . "/$bittFile", $bitt->script);
        $this->remoteTaskService->run("chmod 775 " . self::BITT_FILES . "/$bittFile");

        $this->remoteTaskService->ssh($server, $bitt->user);

        $log = $this->remoteTaskService->run(self::BITT_FILES . '/./' . $bittFile);


        $this->remoteTaskService->ssh($server);
        $this->remoteTaskService->removeFile(self::BITT_FILES . "/$bittFile");

        return $log;
    }

    /**
     * @param Server $server
     * @param Schema $schema
     */
    public function addSchema(Server $server, Schema $schema)
    {
        $this->getService(SystemService::DATABASE, $server)->addSchema($schema);
    }

    /**
     * @param Server $server
     * @param Schema $schema
     */
    public function removeSchema(Server $server, Schema $schema)
    {
        $this->getService(SystemService::DATABASE, $server)->removeSchema($schema);
    }

    /**
     * @param Server $server
     * @param SchemaUser $schemaUser
     */
    public function addSchemaUser(Server $server, SchemaUser $schemaUser)
    {
        $this->getService(SystemService::DATABASE, $server)->addSchemaUser($schemaUser);
    }

    /**
     * @param Server $server
     * @param SchemaUser $schemaUser
     */
    public function removeSchemaUser(Server $server, SchemaUser $schemaUser)
    {
        $this->getService(SystemService::DATABASE, $server)->removeSchemaUser($schemaUser);
    }

    /**
     * @param Server $server
     * @param EnvironmentVariable $environmentVariable
     * @throws FailedCommand
     * @throws SshConnectionFailed
     * @throws \Exception
     */
    public function addEnvironmentVariable(Server $server, EnvironmentVariable $environmentVariable)
    {
        $this->remoteTaskService->ssh($server);
        $value = str_replace('"', '\\"', $environmentVariable->value);
        $this->remoteTaskService->appendTextToFile('/etc/environment', "$environmentVariable->variable=\"$value\"");
    }

    /**
     * @param Server $server
     * @param EnvironmentVariable $environmentVariable
     * @throws FailedCommand
     * @throws SshConnectionFailed
     * @throws \Exception
     */
    public function removeEnvironmentVariable(Server $server, EnvironmentVariable $environmentVariable)
    {
        $this->remoteTaskService->ssh($server);
        $this->remoteTaskService->removeLineByText('/etc/environment', $environmentVariable->variable);
    }

    /**
     * @param Server $server
     * @param LanguageSetting $languageSetting
     */
    public function runLanguageSetting(Server $server, LanguageSetting $languageSetting)
    {
        $systemService = $this->getService('Languages\\'.$languageSetting->language.'\\'.$languageSetting->language.'Settings', $server);

        call_user_func_array([$systemService, $languageSetting->setting], $languageSetting->params);
    }

    /**
     * @param Server $server
     * @param $newSudoPassword
     * @throws FailedCommand
     * @throws SshConnectionFailed
     * @throws \Exception
     */
    public function updateSudoPassword(Server $server, $newSudoPassword)
    {
        $this->remoteTaskService->ssh($server);
        $this->remoteTaskService->run('echo \'codepier:' . $newSudoPassword . '\' | chpasswd');
    }
}
