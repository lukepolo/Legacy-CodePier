<?php

namespace App\Services\Server;

use App\Contracts\RemoteTaskServiceContract as RemoteTaskService;
use App\Contracts\Server\ProvisionServiceContract as ProvisionService;
use App\Contracts\Server\ServerServiceContract;
use App\Events\ServerProvisioned;
use App\Exceptions\SshConnectionFailed;
use App\Models\Server;
use App\Models\ServerCronJob;
use App\Models\ServerDaemon;
use App\Models\ServerFirewallRule;
use App\Models\ServerProvider;
use App\Models\User;
use phpseclib\Crypt\RSA;

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
     *
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
     *
     * @param ServerProvider $serverProvider
     * @param User $user
     * @param $name
     * @param array $options
     * @return mixed
     */
    public function create(ServerProvider $serverProvider, User $user, $name, array $options)
    {
        return $this->getProvider($serverProvider)->create($user, $name, $this->createSshKey(), $options);
    }

    private function createSshKey()
    {
        $rsa = new RSA();
        $rsa->setPublicKeyFormat(RSA::PUBLIC_FORMAT_OPENSSH);

        return $rsa->createKey();
    }

    /**
     * Gets the server options available (ram, cpus , disk space, etc.)
     *
     * @param ServerProvider $serverProvider
     * @return mixed
     */
    public function getServerOptions(ServerProvider $serverProvider)
    {
        return $this->getProvider($serverProvider)->getOptions();
    }

    /**
     * Gets the servers regions available
     *
     * @param ServerProvider $serverProvider
     * @return mixed
     */
    public function getServerRegions(ServerProvider $serverProvider)
    {
        return $this->getProvider($serverProvider)->getRegions();
    }

    /**
     * Gets the status of the serer
     *
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
     *
     * @param Server $server
     * @return mixed
     */
    public function saveInfo(Server $server)
    {
        return $this->getProvider($server->serverProvider)->savePublicIP($server);
    }

    /**
     * Installs a SSH key onto a server
     *
     * @param Server $server
     * @param $sshKey
     */
    public function installSshKey(Server $server, $sshKey)
    {
        $this->remoteTaskService->ssh($server);
        $this->remoteTaskService->run('echo ' . $sshKey . ' >> ~/.ssh/authorized_keys');
        $this->remoteTaskService->run('echo ' . $sshKey . ' >> /home/codepier/.ssh/authorized_keys');
    }

    /**
     * Removes an SSH key from the server
     *
     * @param Server $server
     * @param $sshKey
     */
    public function removeSshKey(Server $server, $sshKey)
    {
        $this->remoteTaskService->ssh($server);

        $sshKey = str_replace('/', '\/', $sshKey);
        $this->remoteTaskService->run("sed -i '/$sshKey/d' ~/.ssh/authorized_keys");
        $this->remoteTaskService->run("sed -i '/$sshKey/d' /home/codepier/.ssh/authorized_keys");
    }

    /**
     * Provisions a server
     *
     * @param Server $server
     */
    public function provision(Server $server)
    {
        $sudoPassword = str_random(32);
        $databasePassword = str_random(32);

        $this->provisionService->provision($server, $sudoPassword, $databasePassword);

        $server->status = 'Provisioned';
        $server->save();

        event(new ServerProvisioned($server, $sudoPassword, $databasePassword));
    }

    /**
     * Gets the provider passed in
     *
     * @param ServerProvider $serverProvider
     * @return mixed
     */
    private function getProvider(ServerProvider $serverProvider)
    {
        return new $this->providers[$serverProvider->provider_name]();
    }

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

    public function removeCron(Server $server, ServerCronJob $cronJob)
    {
        $this->remoteTaskService->ssh($server);
        $this->remoteTaskService->run('crontab -l | grep -v "* * * * * date >/dev/null 2>&1" | crontab -');

        $cronJob->delete();
    }

    public function addFirewallRule(Server $server, $port, $description)
    {
        ServerFirewallRule::create([
            'description' => $description,
            'server_id' => $server->id,
            'port' => $port
        ]);

        $this->remoteTaskService->ssh($server);
        $this->remoteTaskService->run("sed -i '/# DO NOT REMOVE - Custom Rules/a iptables -A INPUT -p tcp -m tcp --dport $port -j ACCEPT' /opt/iptables");

        $this->rebuildFirewall($server);
    }

    public function removeFirewallRule(Server $server, ServerFirewallRule $firewallRule)
    {
        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->run("sed -i '/iptables -A INPUT -p tcp -m tcp --dport $firewallRule->port -j ACCEPT/d ' /opt/iptables");

        $firewallRule->delete();

        $this->rebuildFirewall($server);
    }

    private function rebuildFirewall(Server $server)
    {
        $this->remoteTaskService->ssh($server);
        $this->remoteTaskService->run('./opt/iptables');
    }

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

        $this->remoteTaskService->run('
cat > /etc/supervisor/conf.d/worker-' . $serverDaemon->id . '.conf <<    \'EOF\'
[program:worker-' . $serverDaemon->id . ']
process_name=%(program_name)s_%(process_num)02d
command=' . $command . '
autostart=' . $autoStart . '
autorestart=' . $autoRestart . '
user=' . $user . '
numprocs=' . $numberOfWorkers . '
redirect_stderr=true
stdout_logfile=/home/codepier/workers/worker-' . $serverDaemon->id . '.log
EOF
echo "Wrote" ');

        $this->remoteTaskService->run('supervisorctl reread');
        $this->remoteTaskService->run('supervisorctl update');
        $this->remoteTaskService->run('supervisorctl start worker-' . $serverDaemon->id . ':*');

    }

    public function removeDaemon(Server $server, ServerDaemon $serverDaemon)
    {
        $this->remoteTaskService->ssh($server);

        $this->remoteTaskService->run('rm /etc/supervisor/conf.d/worker-' . $serverDaemon->id . '.conf');

        $this->remoteTaskService->run('supervisorctl reread');
        $this->remoteTaskService->run('supervisorctl update');


        $serverDaemon->delete();
    }

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
}