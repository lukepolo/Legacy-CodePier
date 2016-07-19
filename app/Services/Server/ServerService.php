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
use App\Models\ServerNetworkRule;
use App\Models\ServerProvider;
use App\Models\Site;
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
     * Creates a new server
     * @param ServerProvider $serverProvider
     * @param Server $server
     * @return mixed
     */
    public function create(ServerProvider $serverProvider, Server $server)
    {
        return $this->getProvider($serverProvider)->create($server, $this->createSshKey());
    }

    /**
     * Gets the server options available (ram, cpus , disk space, etc.)
     * @param ServerProvider $serverProvider
     * @return mixed
     */
    public function getServerOptions(ServerProvider $serverProvider)
    {
        return $this->getProvider($serverProvider)->getOptions();
    }

    /**
     * Gets the servers regions available
     * @param ServerProvider $serverProvider
     * @return mixed
     */
    public function getServerRegions(ServerProvider $serverProvider)
    {
        return $this->getProvider($serverProvider)->getRegions();
    }

    /**
     * Gets the status of the serer
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
     * Saves the server information into the database
     * @param Server $server
     * @return mixed
     */
    public function saveInfo(Server $server)
    {
        return $this->getProvider($server->serverProvider)->savePublicIP($server);
    }

    /**
     * Installs a SSH key onto a server
     * @param Server $server
     * @param $sshKey
     * @return bool
     */
    public function installSshKey(Server $server, $sshKey)
    {
        try {
            $this->remoteTaskService->ssh($server);
            $this->remoteTaskService->run('echo ' . $sshKey . ' >> /home/codepier/.ssh/authorized_keys');
        } catch (SshConnectionFailed $e) {
            return false;
        }
    }

    /**
     * Removes an SSH key from the server
     * @param Server $server
     * @param $sshKey
     * @return bool
     */
    public function removeSshKey(Server $server, $sshKey)
    {
        try {
            $this->remoteTaskService->ssh($server);

            $sshKey = str_replace('/', '\/', $sshKey);
            $this->remoteTaskService->run("sed -i '/$sshKey/d' /home/codepier/.ssh/authorized_keys");
        } catch (SshConnectionFailed $e) {
            return false;
        }
    }

    /**
     * Provisions a server
     * @param Server $server
     */
    public function provision(Server $server)
    {
        $sudoPassword = str_random(32);
        $databasePassword = str_random(32);

        $this->provisionService->provision($server, $sudoPassword, $databasePassword);

        app(Dispatcher::class)->dispatchNow(new CreateSite($server));

        event(new ServerProvisioned($server, $sudoPassword, $databasePassword));
    }

    /**
     * Gets the provider passed in
     * @param ServerProvider $serverProvider
     * @return mixed
     */
    private function getProvider(ServerProvider $serverProvider)
    {
        return new $this->providers[$serverProvider->provider_name]();
    }

    /**
     * Installs a cron job
     * @param Server $server
     * @param $cronJob
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
    }

    /**
     * Removes a cron job
     * @param Server $server
     * @param ServerCronJob $cronJob
     * @throws SshConnectionFailed
     * @throws \Exception
     */
    public function removeCron(Server $server, ServerCronJob $cronJob)
    {
        $this->remoteTaskService->ssh($server);
        $this->remoteTaskService->run('crontab -l | grep -v "* * * * * date >/dev/null 2>&1" | crontab -');

        $cronJob->delete();
    }

    /**
     * Adds a firewall rule
     * @param Server $server
     * @param $fromIP
     * @param $port
     * @param $description
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

        if(empty($fromIP)) {
            $this->remoteTaskService->run("sed -i '/# DO NOT REMOVE - Custom Rules/a iptables -A INPUT -p tcp -m tcp --dport $port -j ACCEPT' /etc/opt/iptables");
        } else {
            $this->remoteTaskService->run("sed -i '/# DO NOT REMOVE - Custom Rules/a iptables -A INPUT -s $fromIP -p tcp -m tcp --dport $port -j ACCEPT' /etc/opt/iptables");
        }

        $this->rebuildFirewall($server);
    }

    public function addServerNetworkRule(Server $server, $serverIP)
    {
        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->run("sed -i '/# DO NOT REMOVE - Custom Rules/a iptables -A INPUT -s $serverIP -j ACCEPT' /etc/opt/iptables");

        $this->rebuildFirewall($server);
    }

    public function removeServerNetworkRule(Server $server, $serverIP)
    {
        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->run("sed -i '/iptables -A INPUT -s $serverIP -j ACCEPT/d ' /etc/opt/iptables");

        $this->rebuildFirewall($server);
    }

    /**
     * Removes a rule from the firewall
     * @param Server $server
     * @param ServerFirewallRule $firewallRule
     * @throws SshConnectionFailed
     * @throws \Exception
     */
    public function removeFirewallRule(Server $server, ServerFirewallRule $firewallRule)
    {
        $this->remoteTaskService->ssh($server);

        if(empty($firewallRule->from_ip)) {
            $this->remoteTaskService->run("sed -i '/iptables -A INPUT -p tcp -m tcp --dport $firewallRule->port -j ACCEPT/d ' /etc/opt/iptables");
        } else {
            $this->remoteTaskService->run("sed -i '/iptables -A INPUT -s $firewallRule->from_ip -p tcp -m tcp --dport $firewallRule->port -j ACCEPT/d ' /etc/opt/iptables");
        }


        $firewallRule->delete();

        $this->rebuildFirewall($server);
    }

    /**
     * Rebuilds the firewall
     * @param Server $server
     * @throws SshConnectionFailed
     */
    private function rebuildFirewall(Server $server)
    {
        $this->remoteTaskService->ssh($server);
        $this->remoteTaskService->run('/etc/opt/./iptables');
    }

    /**
     * Installs a daemon
     * @param Server $server
     * @param $command
     * @param $autoStart
     * @param $autoRestart
     * @param $user
     * @param $numberOfWorkers
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

    }

    /**
     * Removes a daemon
     * @param Server $server
     * @param ServerDaemon $serverDaemon
     */
    public function removeDaemon(Server $server, ServerDaemon $serverDaemon)
    {
        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->run('rm /etc/supervisor/conf.d/server-worker-' . $serverDaemon->id . '.conf');

        $this->remoteTaskService->run('supervisorctl reread');
        $this->remoteTaskService->run('supervisorctl update');


        $serverDaemon->delete();
    }

    /**
     * Tests the ssh connection from codepier to the server
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
     * Gets a file on the server
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
     * Creates an ssh key for the server
     * @return array
     */
    private function createSshKey()
    {
        $rsa = new RSA();
        $rsa->setPublicKeyFormat(RSA::PUBLIC_FORMAT_OPENSSH);

        return $rsa->createKey();
    }

    public function restartWebServerServices(Server $server)
    {
        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->run('service nginx restart');
    }

    public function restartDatabase(Server $server)
    {
        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->run('service mysql restart');

    }

    public function restartServer(Server $server)
    {
        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->run('reboot now', false, true);
    }

    public function restartWorkers(Server $server)
    {
        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->run('supervisorctl restart all');
    }

}