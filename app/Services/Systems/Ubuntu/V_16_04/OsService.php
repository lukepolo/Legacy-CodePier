<?php

namespace App\Services\Systems\Ubuntu\V_16_04;

use App\Services\Systems\Traits\ServiceConstructorTrait;

class OsService
{
    use ServiceConstructorTrait;

    public function updateSystem()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get update');
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get -y upgrade');
    }

    public function setTimezoneToUTC()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('ln -sf /usr/share/zoneinfo/UTC /etc/localtime');
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

        $rootPassword = decrypt($this->server->root_password);

        $this->remoteTaskService->run('echo \'root:'.$rootPassword.'\' | chpasswd');

        $this->remoteTaskService->run('adduser --disabled-password --gecos "" codepier');

        $this->remoteTaskService->run('echo \'codepier:'.$rootPassword.'\' | chpasswd');
        $this->remoteTaskService->run('adduser codepier sudo');
        $this->remoteTaskService->run('usermod -a -G www-data codepier');

        $this->remoteTaskService->run('mkdir /home/codepier/.ssh && cp -a ~/.ssh/authorized_keys /home/codepier/.ssh/authorized_keys');
        $this->remoteTaskService->run('chmod 700 /home/codepier/.ssh && chmod 600 /home/codepier/.ssh/authorized_keys');

        $this->remoteTaskService->writeToFile('/home/codepier/.ssh/id_rsa', $this->server->private_ssh_key);
        $this->remoteTaskService->writeToFile('/home/codepier/.ssh/id_rsa.pub', $this->server->public_ssh_key);

        $this->remoteTaskService->updateText('/etc/ssh/sshd_config', '#PasswordAuthentication', 'PasswordAuthentication no');
        $this->remoteTaskService->run('chown codepier:codepier /home/codepier/.ssh -R');

        $this->remoteTaskService->run('service sshd restart');
    }

    public function createSwap()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('fallocate -l 1G /swapfile');
        $this->remoteTaskService->run('chmod 600 /swapfile');
        $this->remoteTaskService->run('mkswap /swapfile');
        $this->remoteTaskService->run('swapon /swapfile');
        $this->remoteTaskService->run('cp /etc/fstab /etc/fstab.bak');
        $this->remoteTaskService->run('echo \'/swapfile none swap sw 0 0\' | tee -a /etc/fstab');
        $this->remoteTaskService->run('echo "vm.swappiness=10" >> /etc/sysctl.conf');
        $this->remoteTaskService->run('echo "vm.vfs_cache_pressure=50" >> /etc/sysctl.conf');
    }

    public function installCertBot()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y letsencrypt');
    }

    public function resetSudoPassword()
    {
        $this->connectToServer();

        $password = str_random(32);

        $this->server->root_password = encrypt($password);

        $this->server->save();

        $this->remoteTaskService->run("echo \"codepier:$password\" | chpasswd");
    }
}
