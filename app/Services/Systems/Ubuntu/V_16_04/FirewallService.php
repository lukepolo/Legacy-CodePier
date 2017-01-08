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
        $this->remoteTaskService->run('echo "y" | ufw enable');
    }

    public function addFirewallRule(FirewallRule $firewallRule)
    {
        $this->connectToServer();

        $this->addBasicFirewallRules();

        if ($firewallRule->from_ip) {
            return $this->remoteTaskService->run("ufw allow $firewallRule->port/$firewallRule->type from $firewallRule->from_ip");
        }

        return $this->remoteTaskService->run("ufw allow $firewallRule->port/$firewallRule->type");
    }

    public function removeFirewallRule(FirewallRule $firewallRule)
    {
        $this->connectToServer();

        if ($firewallRule->from_ip) {
            return $this->remoteTaskService->run("ufw delete allow $firewallRule->port/$firewallRule->type from $firewallRule->from_ip");
        }

        return $this->remoteTaskService->run("ufw delete allow $firewallRule->port/$firewallRule->type");
    }

    public function addServerNetworkRule($serverIP)
    {
        $this->connectToServer();

        return $this->remoteTaskService->run("ufw allow from $serverIP");
    }

    public function removeServerNetworkRule($serverIP)
    {
        $this->connectToServer();

        return $this->remoteTaskService->run("ufw deny from $serverIP");
    }
}
