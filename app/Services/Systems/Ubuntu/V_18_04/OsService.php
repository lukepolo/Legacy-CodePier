<?php

namespace App\Services\Systems\Ubuntu\V_18_04;

use App\Services\Systems\SystemService;
use App\Services\Systems\ServiceConstructorTrait;

class OsService
{
    use ServiceConstructorTrait;

    public function updateSystem()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('sleep 45; DEBIAN_FRONTEND=noninteractive apt-get update');
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get -y upgrade');

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get -y install zip unzip libpng-dev libpq-dev software-properties-common apt-transport-https');

        // https://community.rackspace.com/products/f/25/t/5110
        $this->remoteTaskService->updateText('/etc/gai.conf', '#precedence ::ffff:0:0/96  100', 'precedence ::ffff:0:0/96  100');

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt autoremove -y');
    }

    public function setTimezoneToUTC()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('ln -sf /usr/share/zoneinfo/UTC /etc/localtime');

        $this->remoteTaskService->run('timedatectl set-ntp on');
    }

    public function setLocaleToUTF8()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('echo "LC_ALL=en_US.UTF-8" >> /etc/default/locale');
        $this->remoteTaskService->run('locale-gen en_US.UTF-8');
    }

    public function addCodePierUser()
    {
        $this->connectToServer();

        $rootPassword = $this->server->sudo_password;

        $this->remoteTaskService->run('adduser --disabled-password --gecos "" codepier');

        $this->remoteTaskService->run('echo \'codepier:' . $rootPassword . '\' | chpasswd');
        $this->remoteTaskService->run('adduser codepier sudo');
        $this->remoteTaskService->run('usermod -a -G www-data codepier');

        $this->remoteTaskService->run('mkdir /home/codepier/.ssh && cp -a ~/.ssh/authorized_keys /home/codepier/.ssh/authorized_keys');
        $this->remoteTaskService->run('chmod 700 /home/codepier/.ssh');

        if (config('app.env') === 'local') {
            $this->remoteTaskService->appendTextToFile('/home/codepier/.ssh/authorized_keys', env('DEV_SSH_KEY'));
        }

        $this->remoteTaskService->writeToFile('/home/codepier/.ssh/id_rsa', $this->server->private_ssh_key);
        $this->remoteTaskService->writeToFile('/home/codepier/.ssh/id_rsa.pub', $this->server->public_ssh_key);

        $this->remoteTaskService->run('chmod 600 /home/codepier/.ssh/* -R');

        $this->remoteTaskService->updateText('/etc/ssh/sshd_config', 'PasswordAuthentication yes', 'PasswordAuthentication no');

        $this->remoteTaskService->run('chown codepier:codepier /home/codepier/.ssh -R');
        $this->remoteTaskService->run('chmod o-w /home/codepier');

        $this->remoteTaskService->run('service sshd restart');

        $this->remoteTaskService->makeDirectory('/opt/codepier');

        $this->addToServiceRestartGroup(SystemService::WEB_SERVICE_GROUP, '');
        $this->addToServiceRestartGroup(SystemService::WORKER_SERVICE_GROUP, '');
        $this->addToServiceRestartGroup(SystemService::DATABASE_SERVICE_GROUP, '');
        $this->addToServiceRestartGroup(SystemService::DEPLOYMENT_SERVICE_GROUP, '');

        $this->addToServiceRestartGroup(SystemService::WORKER_PROGRAMS_GROUP, '');
        $this->addToServiceRestartGroup(SystemService::DAEMON_PROGRAMS_GROUP, '');
    }

    public function getPrivateIpAddresses()
    {
        $this->connectToServer();

        $privateIps = trim($this->remoteTaskService->run("ifconfig | grep 'inet addr' | cut -d ':' -f 2 | awk '{ print $1 }' | grep -E '^(192\.168|10\.|172\.1[6789]\.|172\.2[0-9]\.|172\.3[01]\.)'", true));

        if (! empty($privateIps)) {
            $this->server->update([
                'private_ips' => array_filter(array_map('trim', explode(' ', $privateIps)))
            ]);
        }
    }

    public function addAutoRemovalCronJob()
    {
        $this->connectToServer();

        $cronJob = 'DEBIAN_FRONTEND=noninteractive apt autoremove -y';

        $this->remoteTaskService->run('crontab -l | (grep ' . $cronJob . ') || ((crontab -l; echo "' . $cronJob . ' > /dev/null 2>&1") | crontab)');

        $this->createCronJob($this->server, $cronJob, 'root');
    }

    /**
     *  @description SWAP is a virtual space to help guard against memory errors in applications.
     *
     * @size { "suffix" : "GB" }
     * @swappiness { "type" : "number" }
     * @vfsCachePressure { "type" : "number" }
     */
    public function installSwap($size = 2, $swappiness = 10, $vfsCachePressure = 50)
    {
        if (! is_integer($size)) {
            $size = 2;
        }
        $this->connectToServer();

        $this->remoteTaskService->run('fallocate -l ' . $size . 'G /swapfile');
        $this->remoteTaskService->run('chmod 600 /swapfile');
        $this->remoteTaskService->run('mkswap /swapfile');
        $this->remoteTaskService->run('swapon /swapfile');
        $this->remoteTaskService->run('cp /etc/fstab /etc/fstab.bak');
        $this->remoteTaskService->run('echo \'/swapfile none swap sw 0 0\' | tee -a /etc/fstab');
        $this->remoteTaskService->run('echo "vm.swappiness=' . $swappiness . '" >> /etc/sysctl.conf');
        $this->remoteTaskService->run('echo "vm.vfs_cache_pressure=' . $vfsCachePressure . '" >> /etc/sysctl.conf');
    }

    public function resetSudoPassword()
    {
        $this->connectToServer();

        $password = $this->server->sudo_password = str_random(32);

        $this->server->save();

        $this->remoteTaskService->run("echo \"codepier:$password\" | chpasswd");
    }

    public function setupUnattendedSecurityUpgrades()
    {
        $this->connectToServer();

        $this->remoteTaskService->appendTextToFile('/etc/apt/apt.conf.d/50unattended-upgrades', '
Unattended-Upgrade::Allowed-Origins {
    "Ubuntu xenial-security";
};
Unattended-Upgrade::Package-Blacklist {
    //
};
');

        $this->remoteTaskService->appendTextToFile('/etc/apt/apt.conf.d/10periodic', '
APT::Periodic::Update-Package-Lists "1";
APT::Periodic::Download-Upgradeable-Packages "1";
APT::Periodic::AutocleanInterval "7";
APT::Periodic::Unattended-Upgrade "1";
');
    }

    /**
     *  @description Docker is an open-source project that automates the deployment of applications inside software containers.
     */
    public function installDocker()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('sudo apt-key adv --keyserver hkp://p80.pool.sks-keyservers.net:80 --recv-keys 58118E89F3A912897C070ADBF76221572C52609D');
        $this->remoteTaskService->run("sudo apt-add-repository 'deb https://apt.dockerproject.org/repo ubuntu-xenial main'");

        $this->remoteTaskService->run('sudo apt-get update');

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y docker-engine');

        $this->remoteTaskService->run('sudo usermod -aG docker codepier');
    }
}
