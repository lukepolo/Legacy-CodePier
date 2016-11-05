<?php

namespace App\Services\Server;

use App\Classes\DiskSpace;
use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;
use App\Contracts\Server\ServerServiceContract;
use App\Contracts\Systems\SystemServiceContract;
use App\Exceptions\SshConnectionFailed;
use App\Models\Server\Provider\ServerProvider;
use App\Models\Server\Server;
use App\Models\Server\ServerCronJob;
use App\Notifications\ServerProvisioned;
use App\Services\Systems\SystemService;
use phpseclib\Crypt\RSA;
use phpseclib\Net\SFTP;

class ServerService implements ServerServiceContract
{
    protected $systemService;
    protected $remoteTaskService;

    public static $serverOperatingSystem = 'ubuntu-16-04-x64';

    /**
     * SiteService constructor.
     *
     * @param \App\Services\RemoteTaskService | RemoteTaskService $remoteTaskService
     * @param SystemService | SystemServiceContract               $systemService
     */
    public function __construct(RemoteTaskService $remoteTaskService, SystemServiceContract $systemService)
    {
        $this->remoteTaskService = $remoteTaskService;
        $this->systemService = $systemService;
    }

    /**
     * @param \App\Models\Server\ServerProvider $serverProvider
     * @param \App\Models\Server\Server         $server
     *
     * @return mixed
     */
    public function create(\App\Models\Server\Provider\ServerProvider $serverProvider, Server $server)
    {
        return $this->getProvider($serverProvider)->create($server, $this->createSshKey());
    }

    /**
     * @param \App\Models\Server\ServerProvider $serverProvider
     *
     * @return mixed
     */
    public function getServerOptions(\App\Models\Server\Provider\ServerProvider $serverProvider)
    {
        return $this->getProvider($serverProvider)->getOptions();
    }

    /**
     * @param \App\Models\Server\ServerProvider $serverProvider
     *
     * @return mixed
     */
    public function getServerRegions(ServerProvider $serverProvider)
    {
        return $this->getProvider($serverProvider)->getRegions();
    }

    /**
     * @param \App\Models\Server\Server $server
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
            $server->ssh_connection = false;
            $server->save();

            return false;
        }
    }

    /**
     * @return array
     */
    private function createSshKey()
    {
        $rsa = new RSA();
        $rsa->setPublicKeyFormat(RSA::PUBLIC_FORMAT_OPENSSH);

        return $rsa->createKey();
    }

    /**
     * @param \App\Models\Server\Server $server
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
     * @param \App\Models\Server\Server $server
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
     * @param \App\Models\Server\Server $server
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
     * @param \App\Models\Server\Server $server
     *
     * @return array
     */
    public function restartWebServices(Server $server)
    {
        $this->getService(SystemService::WEB, $server)->restartWebServices();
    }

    /**
     * @param \App\Models\Server\Server $server
     *
     * @return bool
     */
    public function restartDatabase(Server $server)
    {
        $this->getService(SystemService::DATABASE, $server)->restartDatabase();
    }

    /**
     * @param \App\Models\Server\Server $server
     *
     * @return bool
     */
    public function restartWorkers(Server $server)
    {
        $this->getService(SystemService::WORKERS, $server)->restartWorkers();
    }

    /**
     * @param \App\Models\Server\Server $server
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
     * @param \App\Models\Server\Server $server
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
     * @param \App\Models\Server\Server $server
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
     * @param \App\Models\Server\Server $server
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
     * @param \App\Models\Server\Server $server
     * @param $sshKey
     *
     * @return bool
     */
    public function installSshKey(Server $server, $sshKey)
    {
        $this->remoteTaskService->ssh($server, 'codepier');

        $this->remoteTaskService->appendTextToFile('/home/codepier/.ssh/authorized_keys', $sshKey);

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param \App\Models\Server\Server $server
     * @param $sshKey
     *
     * @return bool
     */
    public function removeSshKey(Server $server, $sshKey)
    {
        $this->remoteTaskService->ssh($server, 'codepier');

        $this->remoteTaskService->removeLineByText('/home/codepier/.ssh/authorized_keys', $sshKey);

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param \App\Models\Server\Server $server
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
     * @param \App\Models\Server\ServerCronJob $serverCronJob
     *
     * @return array
     */
    public function installCron(ServerCronJob $serverCronJob)
    {
        $this->remoteTaskService->ssh($serverCronJob->server, $serverCronJob->user);
        $this->remoteTaskService->run('crontab -l | (grep '.$serverCronJob->job.') || ((crontab -l; echo "'.$serverCronJob->job.' >/dev/null 2>&1") | crontab)');

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param \App\Models\Server\ServerCronJob $cronJob
     *
     * @return bool
     */
    public function removeCron(ServerCronJob $cronJob)
    {
        $this->remoteTaskService->ssh($cronJob->server, $cronJob->user);
        $this->remoteTaskService->run('crontab -l | grep -v "'.$cronJob->job.' >/dev/null 2>&1" | crontab -');

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param \App\Models\Server\ServerProvider $serverProvider
     *
     * @return mixed
     */
    private function getProvider(ServerProvider $serverProvider)
    {
        return new $serverProvider->provider_class($serverProvider->provider_name);
    }

    public function getService($service, Server $server)
    {
        return $this->systemService->createSystemService($service, $server);
    }

    public function removeSslFiles(Site $site)
    {
        $this->remoteTaskService->removeFile(self::WEB_SERVER_FILES."/$site->domain/before/ssl_redirect.conf");
    }

    /**
     * @param \App\Models\Server\Server             $server
     * @param SiteSslCertificate $siteSslCertificate
     *
     * @return array
     */
    public function installSSL(Server $server, SiteSslCertificate $siteSslCertificate)
    {
        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->run(
            'letsencrypt certonly --non-interactive --agree-tos --email '.$server->user->email.' --webroot -w /home/codepier/ --expand -d '.implode(' -d', explode(',', $siteSslCertificate->domains))
        );

        if (count($errors = $this->remoteTaskService->getErrors())) {
            return $errors;
        }

        $this->remoteTaskService->run('crontab -l | (grep letsencrypt) || ((crontab -l; echo "* */12 * * * letsencrypt renew >/dev/null 2>&1") | crontab)');

        $this->activateSSL($server, $siteSslCertificate);

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param \App\Models\Server\Server $server
     * @param Site   $site
     * @param $key
     * @param $cert
     *
     * @return array
     */
    public function installExistingSSL(Server $server, Site $site, $key, $cert)
    {
        $this->remoteTaskService->ssh($server);

        if ($site->hasActiveSSL()) {
            $activeSSL = $site->activeSSL;
            $activeSSL->active = false;
            $activeSSL->save();
        }

        $siteSLL = SiteSslCertificate::create([
            'site_id' => $site->id,
            'domains' => null,
            'type'    => self::LETS_ENCRYPT,
            'active'  => true,
        ]);

        $sslCertPath = self::SSL_FILES.'/'.$site->domain.'/'.$siteSLL->id;

        $siteSLL->key_path = $sslCertPath.'/server.key';
        $siteSLL->cert_path = $sslCertPath.'/server.crt';
        $siteSLL->save();


        $this->remoteTaskService->writeToFile($siteSLL->key_path, $key);
        $this->remoteTaskService->writeToFile($siteSLL->cert_path, $cert);

        $this->updateWebServerConfig($server, $site);

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param \App\Models\Server\Server $server
     * @param Site   $site
     */
    public function mapSSL(Server $server, Site $site)
    {
        $this->remoteTaskService->ssh($server);

        $activeSSL = $site->load('activeSSL')->activeSSL;

        $sslCertPath = self::SSL_FILES.'/'.$site->domain.'/'.$activeSSL->id;

        $this->remoteTaskService->makeDirectory($sslCertPath);

        $this->remoteTaskService->run("ln -f -s $activeSSL->key_path $sslCertPath/server.key");
        $this->remoteTaskService->run("ln -f -s $activeSSL->cert_path $sslCertPath/server.crt");
    }

    /**
     * @param Server $server
     * @param \App\Models\Site\Site   $site
     *
     * @return array
     */
    public function deactivateSSL(Server $server, Site $site)
    {
        $this->remoteTaskService->ssh($server);

        $site->activeSSL->active = false;
        $site->activeSSL->save();

        $this->updateWebServerConfig($server, $site);

        $this->getWebServerService()->removeSslFiles($site);

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param \App\Models\Server\Server $server
     * @param SiteSslCertificate $siteSslCertificate
     */
    public function removeSSL(Server $server, SiteSslCertificate $siteSslCertificate)
    {
        $this->remoteTaskService->ssh($server);

        switch ($siteSslCertificate->type) {
            case self::LETS_ENCRYPT:
                // leave it be we don't want to erase them cause they aren't unique
                break;
            default:
                $this->remoteTaskService->removeFile($siteSslCertificate->key_path);
                $this->remoteTaskService->removeFile($siteSslCertificate->cert_path);
                break;
        }

        $siteSslCertificate->delete();
    }

    /**
     * @param \App\Models\Server\Server             $server
     * @param \App\Models\Site\SiteSslCertificate $siteSslCertificate
     */
    public function activateSSL(Server $server, SiteSslCertificate $siteSslCertificate)
    {
        $site = $siteSslCertificate->site;

        if ($site->hasActiveSSL()) {
            $site->activeSSL->active = false;
            $site->activeSSL->save();
        }

        $siteSslCertificate->active = true;
        $siteSslCertificate->save();

        $site->load('activeSSL');

        if ($siteSslCertificate->type == self::LETS_ENCRYPT) {
            $this->mapSSL($server, $site);
        }

        $this->updateWebServerConfig($server, $site);
    }
}
