<?php

namespace App\Services\Systems\Ubuntu\V_16_04;

use App\Models\FirewallRule;
use App\Services\Systems\ServiceConstructorTrait;

class FirewallService
{
    use ServiceConstructorTrait;

    protected $lock = 'flock -w 90 /opt/ufw.lock -c';

    public function addBasicFirewallRules()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('ufw default deny incoming');
        $this->remoteTaskService->run('ufw default allow outgoing');
        $this->remoteTaskService->run('ufw allow ssh');
        $this->remoteTaskService->run('ufw disable');
        $this->remoteTaskService->run('ufw allow '.$this->server->port.'/tcp');
        $this->remoteTaskService->run('echo "y" | ufw enable');
    }

    /**
     * @description Fail2Ban is an intrusion prevention software framework that protects computer servers from brute-force attacks.
     */
    public function installFail2ban()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y fail2ban');
        $this->remoteTaskService->run('cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local');
        $this->remoteTaskService->run('service fail2ban restart');
    }

    public function addFirewallRule(FirewallRule $firewallRule)
    {
        $this->connectToServer();

        if ($firewallRule->port !== '*') {
            if (! empty($firewallRule->from_ip)) {
                $command = "ufw allow proto $firewallRule->type from $firewallRule->from_ip to any port $firewallRule->port";
            } else {
                $command = "ufw allow $firewallRule->port/$firewallRule->type";
            }

            return $this->remoteTaskService->run($this->lock.' "'.$command.'"');
        } else {
            return $this->addServerNetworkRule($firewallRule->from_ip);
        }
    }

    public function removeFirewallRule(FirewallRule $firewallRule)
    {
        $this->connectToServer();

        if ($firewallRule->port !== '*') {
            if ($firewallRule->from_ip) {
                return $this->remoteTaskService->run($this->lock.' '."'ufw delete allow proto $firewallRule->type from $firewallRule->from_ip to any port $firewallRule->port'");
            }

            return $this->remoteTaskService->run($this->lock.' '."'ufw delete allow $firewallRule->port/$firewallRule->type'");
        } else {
            return $this->removeServerNetworkRule($firewallRule->from_ip);
        }
    }

    public function addServerNetworkRule($serverIp)
    {
        $this->connectToServer();

        return $this->remoteTaskService->run($this->lock.' '."'ufw allow from $serverIp'");
    }

    public function removeServerNetworkRule($serverIp)
    {
        $this->connectToServer();

        return $this->remoteTaskService->run($this->lock.' '."'ufw delete allow from $serverIp'");
    }
}
