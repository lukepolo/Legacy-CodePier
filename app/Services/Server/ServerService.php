<?php

namespace App\Services\Server;

use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;
use App\Contracts\Server\ProvisionServiceContract as ProvisionService;
use App\Contracts\Server\ServerServiceContract;
use App\Events\ServerProvisioned;
use App\Exceptions\SshConnectionFailed;
use App\Jobs\CreateSite;
use App\Models\Server;
use App\Models\ServerCronJob;
use App\Models\ServerDaemon;
use App\Models\ServerFirewallRule;
use App\Models\ServerProvider;
use Illuminate\Bus\Dispatcher;
use phpseclib\Crypt\RSA;
use phpseclib\Net\SFTP;

/**
 * Class ServerService
 * @package App\Services
 */
class ServerService implements ServerServiceContract
{
    protected $remoteTaskService;
    protected $provisionService;

    public $providers = [
        'digitalocean' => Providers\DigitalOceanProvider::class
    ];

    public static $serverOperatingSystem = 'ubuntu-16-04-x64';

    /**
     * SiteService constructor.
     * @param \App\Services\RemoteTaskService | RemoteTaskService $remoteTaskService
     * @param \App\Services\Server\ProvisionService | ProvisionService $provisionService
     */
    public function __construct(RemoteTaskService $remoteTaskService, ProvisionService $provisionService)
    {
        $this->remoteTaskService = $remoteTaskService;
        $this->provisionService = $provisionService;
    }

    /**
     * @param ServerProvider $serverProvider
     * @param Server $server
     * @return mixed
     */
    public function create(ServerProvider $serverProvider, Server $server)
    {
        return $this->getProvider($serverProvider)->create($server, $this->createSshKey());
    }

    /**
     * @param ServerProvider $serverProvider
     * @return mixed
     */
    public function getServerOptions(ServerProvider $serverProvider)
    {
        return $this->getProvider($serverProvider)->getOptions();
    }

    /**
     * @param ServerProvider $serverProvider
     * @return mixed
     */
    public function getServerRegions(ServerProvider $serverProvider)
    {
        return $this->getProvider($serverProvider)->getRegions();
    }

    /**
     * @param Server $server
     * @return mixed
     */
    public function getStatus(Server $server)
    {
        $server->touch();

        try {
            return $this->getProvider($server->serverProvider)->getStatus($server);
        } catch (\Exception $e) {
            if ($e->getMessage() == 'The resource you were accessing could not be found.') {
                $server->delete();
                return 'Server Has Been Deleted';
            }
        }
    }

    /**
     * @param Server $server
     * @return mixed
     */
    public function saveInfo(Server $server)
    {
        return $this->getProvider($server->serverProvider)->savePublicIP($server);
    }

    /**
     * @param Server $server
     * @param $sshKey
     * @return bool
     */
    public function installSshKey(Server $server, $sshKey)
    {
        $this->remoteTaskService->ssh($server);
        return $this->remoteTaskService->appendTextToFile('/home/codepier/.ssh/authorized_keys', $sshKey);
    }

    /**
     * @param Server $server
     * @param $sshKey
     * @return bool
     */
    public function removeSshKey(Server $server, $sshKey)
    {
        try {
            $this->remoteTaskService->ssh($server);

            $this->remoteTaskService->removeLineByText('/home/codepier/.ssh/authorized_keys', $sshKey);

        } catch (SshConnectionFailed $e) {
            return false;
        }
    }

    /**
     * @param Server $server
     */
    public function provision(Server $server)
    {
        $sudoPassword = str_random(32);
        $databasePassword = str_random(32);

        $errors = $this->provisionService->provision($server, $sudoPassword, $databasePassword);

        app(Dispatcher::class)->dispatchNow(new CreateSite($server));

        event(new ServerProvisioned($server, $sudoPassword, $databasePassword, $errors));
    }

    /**
     * @param ServerProvider $serverProvider
     * @return mixed
     */
    private function getProvider(ServerProvider $serverProvider)
    {
        return new $this->providers[$serverProvider->provider_name]();
    }

    /**
     * @param Server $server
     * @param $cronJob
     * @return array
     * @throws SshConnectionFailed
     */
    public function installCron(Server $server, $cronJob)
    {
        ServerCronJob::create([
            'server_id' => $server->id,
            'job' => $cronJob,
            'user' => 'root'
        ]);

        $this->remoteTaskService->ssh($server);
        $this->remoteTaskService->run('crontab -l | (grep ' . $cronJob . ' && echo "found") || ((crontab -l; echo "' . $cronJob . ' >/dev/null 2>&1") | crontab)');

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param Server $server
     * @param ServerCronJob $cronJob
     * @return bool
     * @throws SshConnectionFailed
     * @throws \Exception
     */
    public function removeCron(Server $server, ServerCronJob $cronJob)
    {
        $this->remoteTaskService->ssh($server);
        $errors = $this->remoteTaskService->run('crontab -l | grep -v "* * * * * date >/dev/null 2>&1" | crontab -');

        if(empty($errors)) {
            $cronJob->delete();
        }

        return $errors;
    }

    /**
     * @param Server $server
     * @param $fromIP
     * @param $port
     * @param $description
     * @return array
     * @throws SshConnectionFailed
     */
    public function addFirewallRule(Server $server, $fromIP, $port, $description)
    {
        ServerFirewallRule::create([
            'description' => $description,
            'server_id' => $server->id,
            'port' => $port,
            'from_ip' => $fromIP
        ]);

        $this->remoteTaskService->ssh($server);

        if (empty($fromIP)) {
            $this->remoteTaskService->findTextAndAppend('/etc/opt/iptables', '# DO NOT REMOVE - Custom Rules', "iptables -A INPUT -p tcp -m tcp --dport $port -j ACCEPT");
        } else {
            $this->remoteTaskService->removeLineByText('/etc/opt/iptables', "iptables -A INPUT -s $fromIP -p tcp -m tcp --dport $port -j ACCEPT");
        }

        $this->rebuildFirewall($server);

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param Server $server
     * @param $serverIP
     * @return array
     */
    public function addServerNetworkRule(Server $server, $serverIP)
    {
        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->findTextAndAppend('/etc/opt/iptables', '# DO NOT REMOVE - Custom Rules', "iptables -A INPUT -s $serverIP -j ACCEPT");

        $this->rebuildFirewall($server);

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param Server $server
     * @param $serverIP
     * @return array
     */
    public function removeServerNetworkRule(Server $server, $serverIP)
    {
        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->removeLineByText('/etc/opt/iptables', "iptables -A INPUT -s $serverIP -j ACCEPT");

        $this->rebuildFirewall($server);

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param Server $server
     * @param ServerFirewallRule $firewallRule
     * @return bool
     * @throws SshConnectionFailed
     * @throws \Exception
     */
    public function removeFirewallRule(Server $server, ServerFirewallRule $firewallRule)
    {
        $this->remoteTaskService->ssh($server);

        if (empty($firewallRule->from_ip)) {
            $errors = $this->remoteTaskService->removeLineByText('/etc/opt/iptables', "iptables -A INPUT -p tcp -m tcp --dport $firewallRule->port -j ACCEPT");
        } else {
            $errors = $this->remoteTaskService->removeLineByText('/etc/opt/iptables', "iptables -A INPUT -s $firewallRule->from_ip -p tcp -m tcp --dport $firewallRule->port -j ACCEPT");
        }


        if(empty($errors)) {
            $firewallRule->delete();
            return $this->rebuildFirewall($server);
        }

        return $errors;
    }

    /**
     * @param Server $server
     * @return bool
     * @throws SshConnectionFailed
     */
    private function rebuildFirewall(Server $server)
    {
        $this->remoteTaskService->ssh($server);
        return $this->remoteTaskService->run('/etc/opt/./iptables');
    }

    /**
     * @param Server $server
     * @param $command
     * @param $autoStart
     * @param $autoRestart
     * @param $user
     * @param $numberOfWorkers
     * @return array
     */
    public function installDaemon(Server $server, $command, $autoStart, $autoRestart, $user, $numberOfWorkers)
    {
        $serverDaemon = ServerDaemon::create([
            'server_id' => $server->id,
            'command' => $command,
            'auto_start' => $autoStart,
            'auto_restart' => $autoRestart,
            'user' => $user,
            'number_of_workers' => $numberOfWorkers,
        ]);

        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->writeToFile('/etc/supervisor/conf.d/server-worker-' . $serverDaemon->id . '.conf ', '
[program:server-worker-' . $serverDaemon->id . ']
process_name=%(program_name)s_%(process_num)02d
command=' . $command . '
autostart=' . $autoStart . '
autorestart=' . $autoRestart . '
user=' . $user . '
numprocs=' . $numberOfWorkers . '
redirect_stderr=true
stdout_logfile=/home/codepier/workers/server-worker-' . $serverDaemon->id . '.log
');

        $this->remoteTaskService->run('supervisorctl reread');
        $this->remoteTaskService->run('supervisorctl update');
        $this->remoteTaskService->run('supervisorctl start server-worker-' . $serverDaemon->id . ':*');

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param Server $server
     * @param ServerDaemon $serverDaemon
     * @return array|bool
     */
    public function removeDaemon(Server $server, ServerDaemon $serverDaemon)
    {
        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->run('rm /etc/supervisor/conf.d/server-worker-' . $serverDaemon->id . '.conf');

        $this->remoteTaskService->run('supervisorctl reread');
        $this->remoteTaskService->run('supervisorctl update');


        $errors = $this->remoteTaskService->getErrors();

        if(empty($errors)) {
            $serverDaemon->delete();
            return true;
        }

        return $errors;
    }

    /**
     * @param Server $server
     * @return bool
     */
    public function testSshConnection(Server $server)
    {
        try {
            $this->remoteTaskService->ssh($server);

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
     * @param Server $server
     * @param $filePath
     * @return null|string
     */
    public function getFile(Server $server, $filePath)
    {
        $key = new RSA();
        $key->loadKey($server->private_ssh_key);

        $ssh = new SFTP($server->ip);

        if (!$ssh->login('root', $key)) {
            exit('Login Failed');
        }


        if ($contents = $ssh->get($filePath)) {
            return trim($contents);
        }

        return null;
    }

    /**
     * @param Server $server
     * @param $filePath
     * @param $file
     * @return bool
     */
    public function saveFile(Server $server, $filePath, $file)
    {
        $this->remoteTaskService->ssh($server);

        return $this->remoteTaskService->writeToFile($filePath, str_replace("\r\n", PHP_EOL, $file));
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
     * @param Server $server
     * @return array
     */
    public function restartWebServices(Server $server)
    {
        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->run('service nginx restart');
        $this->remoteTaskService->run('service php7.0-fpm restart');

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param Server $server
     * @return bool
     */
    public function restartDatabase(Server $server)
    {
        $this->remoteTaskService->ssh($server);

        return $this->remoteTaskService->run('service mysql restart');

    }

    /**
     * @param Server $server
     * @return bool
     */
    public function restartServer(Server $server)
    {
        $this->remoteTaskService->ssh($server);

        return $this->remoteTaskService->run('reboot now', false, true);
    }

    /**
     * @param Server $server
     * @return bool
     */
    public function restartWorkers(Server $server)
    {
        $this->remoteTaskService->ssh($server);

        return $this->remoteTaskService->run('supervisorctl restart all');
    }

    /**
     * @param Server $server
     * @param $folder
     * @return bool
     */
    public function removeFolder(Server $server, $folder)
    {
        $this->remoteTaskService->ssh($server);

        return $this->remoteTaskService->removeDirectory($folder);
    }

    /**
     * @param Server $server
     * @param $folder
     * @return bool
     */
    public function createFolder(Server $server, $folder)
    {
        $this->remoteTaskService->ssh($server);

        return $this->remoteTaskService->makeDirectory($folder);
    }
}