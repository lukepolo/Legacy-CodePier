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

/**
 * Class ServerService.
 */
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
}
