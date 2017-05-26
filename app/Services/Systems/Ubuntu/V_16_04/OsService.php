<?php

namespace App\Services\Systems\Ubuntu\V_16_04;

use App\Services\AbstractService;
use App\Services\Systems\ServiceConstructorTrait;

class OsService extends AbstractService
{

    public function updateSystem()
    {
        $this->connectToServer();

        // https://github.com/docker/machine/issues/3358 - sleeping for 30

        $this->remoteTaskService->run([
            'sleep 30; DEBIAN_FRONTEND=noninteractive apt-get update',
            'DEBIAN_FRONTEND=noninteractive apt-get -y upgrade',
            'DEBIAN_FRONTEND=noninteractive apt-get -y install zip unzip libpq-dev',
        ]);
    }

    public function setTimezoneToUTC()
    {
        $this->connectToServer();

        $this->remoteTaskService->run([
            'ln -sf /usr/share/zoneinfo/UTC /etc/localtime',
            'DEBIAN_FRONTEND=noninteractive apt-get install -y ntpdate',
            'ntpdate ntp.ubuntu.com',
        ]);
    }

    public function setLocaleToUTF8()
    {
        $this->connectToServer();

        $this->remoteTaskService->run([
            'echo "LC_ALL=en_US.UTF-8" >> /etc/default/locale',
            'locale-gen en_US.UTF-8',
        ]);
    }

    public function addCodePierUser()
    {
        $this->connectToServer();

        $rootPassword = $this->server->sudo_password;

        $this->remoteTaskService->run([
            'adduser --disabled-password --gecos "" codepier',
            'echo \'codepier:'.$rootPassword.'\' | chpasswd',
            'adduser codepier sudo',
            'usermod -a -G www-data codepier',
            'mkdir /home/codepier/.ssh && cp -a ~/.ssh/authorized_keys /home/codepier/.ssh/authorized_keys',
            'chmod 700 /home/codepier/.ssh',
        ]);

        $this->remoteTaskService->writeToFile('/home/codepier/.ssh/id_rsa', $this->server->private_ssh_key);
        $this->remoteTaskService->writeToFile('/home/codepier/.ssh/id_rsa.pub', $this->server->public_ssh_key);

        $this->remoteTaskService->run('chmod 600 /home/codepier/.ssh/* -R');

        $this->remoteTaskService->updateText('/etc/ssh/sshd_config', 'PasswordAuthentication yes', 'PasswordAuthentication no');

        $this->remoteTaskService->run([
            'chown codepier:codepier /home/codepier/.ssh -R',
            'chmod o-w /home/codepier',
            'service sshd restart'
        ]);

        $this->remoteTaskService->makeDirectory('/opt/codepier');
    }

    /**
     * @description SWAP is a virtual space to help guard against memory errors in applications.
     * @param int $size
     * @param int $swappiness
     * @param int $vfsCachePressure
     */
    public function installSwap(string $size = '1G', int $swappiness = 10, int $vfsCachePressure = 50)
    {
        $this->connectToServer();

        $this->remoteTaskService->run([
            'fallocate -l '.$size.' /swapfile',
            'chmod 600 /swapfile',
            'mkswap /swapfile',
            'swapon /swapfile',
            'cp /etc/fstab /etc/fstab.bak',
            'echo \'/swapfile none swap sw 0 0\' | tee -a /etc/fstab',
            'echo "vm.swappiness='.$swappiness.'" >> /etc/sysctl.conf',
            '\'echo "vm.vfs_cache_pressure=\'.$vfsCachePressure.\'" >> /etc/sysctl.conf\'',
        ]);
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

        $this->remoteTaskService->run([
            'sudo apt-key adv --keyserver hkp://p80.pool.sks-keyservers.net:80 --recv-keys 58118E89F3A912897C070ADBF76221572C52609D',
            "sudo apt-add-repository 'deb https://apt.dockerproject.org/repo ubuntu-xenial main",
            'sudo apt-get update',
            'DEBIAN_FRONTEND=noninteractive apt-get install -y docker-engine',
            'sudo usermod -aG docker codepier'
        ]);

    }
}
