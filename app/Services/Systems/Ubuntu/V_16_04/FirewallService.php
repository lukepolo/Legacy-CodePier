<?php

namespace App\Services\Systems\Ubuntu\V_16_04;

use App\Models\FirewallRule;
use App\Services\Systems\ServiceConstructorTrait;

class FirewallService
{
    use ServiceConstructorTrait;

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

    public function addFirewallRule(FirewallRule $firewallRule)
    {
        $this->connectToServer();

        if ($firewallRule->port !== '*') {
            if (! empty($firewallRule->from_ip)) {
                $command = "ufw allow proto $firewallRule->type from $firewallRule->from_ip to any port $firewallRule->port";
            } else {
                $command = "ufw allow $firewallRule->port/$firewallRule->type";
            }

            return $this->remoteTaskService->run($command);
        } else {
            return $this->addServerNetworkRule($firewallRule->from_ip);
        }
    }

    public function removeFirewallRule(FirewallRule $firewallRule)
    {
        $this->connectToServer();

        if ($firewallRule->port !== '*') {
            if ($firewallRule->from_ip) {
                return $this->remoteTaskService->run("ufw delete allow proto $firewallRule->type from $firewallRule->from_ip to any port $firewallRule->port");
            }

            return $this->remoteTaskService->run("ufw delete allow $firewallRule->port/$firewallRule->type");
        } else {
            return $this->removeServerNetworkRule($firewallRule->from_ip);
        }
    }

    public function addServerNetworkRule($serverIp)
    {
        $this->connectToServer();

        return $this->remoteTaskService->run("ufw allow from $serverIp");
    }

    public function removeServerNetworkRule($serverIp)
    {
        $this->connectToServer();

        return $this->remoteTaskService->run("ufw delete allow from $serverIp");
    }
}
