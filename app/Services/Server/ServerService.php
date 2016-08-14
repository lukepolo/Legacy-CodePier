<?php

namespace App\Services\Server;

use App\Classes\DiskSpace;
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
use App\Models\ServerSshKey;
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
     * @param string $user
     * @return bool
     */
    public function installSshKey(Server $server, $sshKey, $user = 'root')
    {
        $this->remoteTaskService->ssh($server, $user);
        return $this->remoteTaskService->appendTextToFile('/home/codepier/.ssh/authorized_keys', $sshKey);
    }

    /**
     * @param ServerSshKey $serverSshKey
     * @param string $user
     * @return bool
     */
    public function removeSshKey(ServerSshKey $serverSshKey, $user = 'root')
    {
        try {
            $this->remoteTaskService->ssh($serverSshKey->server, $user);

            $this->remoteTaskService->removeLineByText('/home/codepier/.ssh/authorized_keys', $serverSshKey->ssh_key);

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

        event(new ServerProvisioned($server, $sudoPassword, $databasePassword, 'Click here to find out more', $errors));
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
     * @param string $user
     * @return array
     */
    public function installCron(Server $server, $cronJob, $user = 'root')
    {
        ServerCronJob::create([
            'server_id' => $server->id,
            'job' => $cronJob,
            'user' => 'root'
        ]);

        $this->remoteTaskService->ssh($server, $user);
        $this->remoteTaskService->run('crontab -l | (grep ' . $cronJob . ') || ((crontab -l; echo "' . $cronJob . ' >/dev/null 2>&1") | crontab)');

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param ServerCronJob $cronJob
     * @param string $user
     * @return bool
     */
    public function removeCron(ServerCronJob $cronJob, $user = 'root')
    {
        $this->remoteTaskService->ssh($cronJob->server, $user);
        $errors = $this->remoteTaskService->run('crontab -l | grep -v "* * * * * date >/dev/null 2>&1" | crontab -');

        if (empty($errors)) {
            $cronJob->delete();
        }

        return $errors;
    }

    /**
     * @param Server $server
     * @param $fromIP
     * @param $port
     * @param $description
     * @param string $user
     * @return array
     */
    public function addFirewallRule(Server $server, $fromIP, $port, $description, $user = 'root')
    {
        ServerFirewallRule::create([
            'description' => $description,
            'server_id' => $server->id,
            'port' => $port,
            'from_ip' => $fromIP
        ]);

        $this->remoteTaskService->ssh($server, $user);

        if (empty($fromIP)) {
            $this->remoteTaskService->findTextAndAppend('/etc/opt/iptables', '# DO NOT REMOVE - Custom Rules',
                "iptables -A INPUT -p tcp -m tcp --dport $port -j ACCEPT");
        } else {
            $this->remoteTaskService->removeLineByText('/etc/opt/iptables',
                "iptables -A INPUT -s $fromIP -p tcp -m tcp --dport $port -j ACCEPT");
        }

        return $this->rebuildFirewall($server);
    }

    /**
     * @param Server $server
     * @param $serverIP
     * @param string $user
     * @return array
     */
    public function addServerNetworkRule(Server $server, $serverIP, $user = 'root')
    {
        $this->remoteTaskService->ssh($server, $user);

        $this->remoteTaskService->findTextAndAppend('/etc/opt/iptables', '# DO NOT REMOVE - Custom Rules',
            "iptables -A INPUT -s $serverIP -j ACCEPT");

        return $this->rebuildFirewall($server);
    }

    /**
     * @param Server $server
     * @param $serverIP
     * @param string $user
     * @return array
     */
    public function removeServerNetworkRule(Server $server, $serverIP, $user = 'root')
    {
        $this->remoteTaskService->ssh($server, $user);

        $this->remoteTaskService->removeLineByText('/etc/opt/iptables', "iptables -A INPUT -s $serverIP -j ACCEPT");

        return $this->rebuildFirewall($server);
    }

    /**
     * @param ServerFirewallRule $firewallRule
     * @param string $user
     * @return bool
     */
    public function removeFirewallRule(ServerFirewallRule $firewallRule, $user = 'root')
    {
        $server = $firewallRule->server;
        $this->remoteTaskService->ssh($server, $user);

        if (empty($firewallRule->from_ip)) {
            $errors = $this->remoteTaskService->removeLineByText('/etc/opt/iptables',
                "iptables -A INPUT -p tcp -m tcp --dport $firewallRule->port -j ACCEPT");
        } else {
            $errors = $this->remoteTaskService->removeLineByText('/etc/opt/iptables',
                "iptables -A INPUT -s $firewallRule->from_ip -p tcp -m tcp --dport $firewallRule->port -j ACCEPT");
        }

        if (empty($errors)) {
            $firewallRule->delete();
            return $this->rebuildFirewall($server);
        }

        return $errors;
    }

    /**
     * @param Server $server
     * @return bool
     */
    private function rebuildFirewall(Server $server)
    {
        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->run('/etc/opt/./iptables');
        $this->remoteTaskService->run('iptables-save > /etc/iptables/rules.v4');
        $this->remoteTaskService->run('ip6tables-save > /etc/iptables/rules.v6');

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param Server $server
     * @param $command
     * @param $autoStart
     * @param $autoRestart
     * @param $user
     * @param $numberOfWorkers
     * @param string $sshUser
     * @return array
     */
    public function installDaemon(
        Server $server,
        $command,
        $autoStart,
        $autoRestart,
        $user,
        $numberOfWorkers,
        $sshUser = 'root'
    ) {
        $serverDaemon = ServerDaemon::create([
            'server_id' => $server->id,
            'command' => $command,
            'auto_start' => $autoStart,
            'auto_restart' => $autoRestart,
            'user' => $user,
            'number_of_workers' => $numberOfWorkers,
        ]);

        $this->remoteTaskService->ssh($server, $sshUser);

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
     * @param ServerDaemon $serverDaemon
     * @param string $user
     * @return array|bool
     */
    public function removeDaemon(ServerDaemon $serverDaemon, $user = 'root')
    {
        $this->remoteTaskService->ssh($serverDaemon->server, $user);

        $this->remoteTaskService->run('rm /etc/supervisor/conf.d/server-worker-' . $serverDaemon->id . '.conf');

        $this->remoteTaskService->run('supervisorctl reread');
        $this->remoteTaskService->run('supervisorctl update');


        $errors = $this->remoteTaskService->getErrors();

        if (empty($errors)) {
            $serverDaemon->delete();
            return true;
        }

        return $errors;
    }

    /**
     * @param Server $server
     * @param string $user
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
     * @param Server $server
     * @param $filePath
     * @param string $user
     * @return null|string
     */
    public function getFile(Server $server, $filePath, $user = 'root')
    {
        $key = new RSA();
        $key->loadKey($server->private_ssh_key);

        $ssh = new SFTP($server->ip);

        if (!$ssh->login($user, $key)) {
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
     * @param string $user
     * @return bool
     */
    public function saveFile(Server $server, $filePath, $file, $user = 'root')
    {
        $this->remoteTaskService->ssh($server, $user);

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
     * @param string $user
     * @return array
     */
    public function restartWebServices(Server $server, $user = 'root')
    {
        $this->remoteTaskService->ssh($server, $user);

        $this->remoteTaskService->run('service nginx restart');
        $this->remoteTaskService->run('service php7.0-fpm restart');

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param Server $server
     * @param string $user
     * @return bool
     */
    public function restartDatabase(Server $server, $user = 'root')
    {
        $this->remoteTaskService->ssh($server, $user);

        return $this->remoteTaskService->run('service mysql restart');

    }

    /**
     * @param Server $server
     * @param string $user
     * @return bool
     */
    public function restartServer(Server $server, $user = 'root')
    {
        $this->remoteTaskService->ssh($server, $user);

        return $this->remoteTaskService->run('reboot now', false, true);
    }

    /**
     * @param Server $server
     * @param string $user
     * @return bool
     */
    public function restartWorkers(Server $server, $user = 'root')
    {
        $this->remoteTaskService->ssh($server, $user);

        return $this->remoteTaskService->run('supervisorctl restart all');
    }

    /**
     * @param Server $server
     * @param $folder
     * @param string $user
     * @return bool
     */
    public function removeFolder(Server $server, $folder, $user = 'root')
    {
        $this->remoteTaskService->ssh($server, $user);

        return $this->remoteTaskService->removeDirectory($folder);
    }

    /**
     * @param Server $server
     * @param $folder
     * @param string $user
     * @return bool
     */
    public function createFolder(Server $server, $folder, $user = 'root')
    {
        $this->remoteTaskService->ssh($server, $user);

        return $this->remoteTaskService->makeDirectory($folder);
    }

    public function installCheckDiskSpaceCron(Server $server)
    {
        $this->provisionService->installDiskMonitorScript($server);
    }

    public function checkDiskSpace(Server $server)
    {
        $this->remoteTaskService->ssh($server);
        $results = $this->remoteTaskService->run("df / | grep / | awk '{print $2} {print $3} {print $4}'");

        $results = array_filter(explode(PHP_EOL, $results));


        return new DiskSpace($results[0], $results[1], $results[2]);
    }

    // TODO - how to deal with this one off requests?
    public function installBlackFire(Server $server, $serverID, $serverToken)
    {
        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->run('wget -O - https://packagecloud.io/gpg.key | apt-key add -');
        $this->remoteTaskService->run('echo "deb http://packages.blackfire.io/debian any main" | tee /etc/apt/sources.list.d/blackfire.list');
        $this->remoteTaskService->run('apt-get update');
        $this->remoteTaskService->run('apt-get install blackfire-agent blackfire-php');

        $this->remoteTaskService->writeToFile('/etc/blackfire/agent'," 
[blackfire]
ca-cert=
collector=https://blackfire.io
log-file=stderr
log-level=1
server-id=$serverID
server-token=$serverToken
socket=unix:///var/run/blackfire/agent.sock
spec=");

        $this->remoteTaskService->updateText('/etc/blackfire/agent', 'server-id', $serverID);
        $this->remoteTaskService->updateText('/etc/blackfire/agent', 'server-token', $serverToken);

        $this->remoteTaskService->run('/etc/init.d/blackfire-agent restart');

        $this->restartWebServices($server);

    }
}