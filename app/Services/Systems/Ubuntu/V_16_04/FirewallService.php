<?php

namespace App\Services\Systems\Ubuntu\V_16_04;

use App\Models\ServerFirewallRule;
use App\Services\Systems\ServiceConstructorTrait;

class FirewallService
{
    use ServiceConstructorTrait;

    protected $defaults = [

    ];

    public function addBasicFirewallRules()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y fail2ban iptables-persistent');

        ServerFirewallRule::create([
            'server_id'   => $this->server->id,
            'description' => 'HTTP',
            'port'        => '80',
            'from_ip'     => null,
        ]);

        ServerFirewallRule::create([
            'server_id'   => $this->server->id,
            'description' => 'HTTPS',
            'port'        => '443',
            'from_ip'     => null,
        ]);

        $this->remoteTaskService->writeToFile('/etc/opt/iptables', '
#!/bin/sh

echo "REDOING IP TABLES"

iptables -F
iptables -X
iptables -t nat -F
iptables -t nat -X
iptables -t mangle -F
iptables -t mangle -X

iptables -P INPUT ACCEPT
iptables -P FORWARD ACCEPT
iptables -P OUTPUT ACCEPT

iptables -A INPUT -m conntrack --ctstate RELATED,ESTABLISHED -j ACCEPT
iptables -I INPUT 1 -i lo -j ACCEPT

# SSH
iptables -A INPUT -p tcp -m tcp --dport 22 -j ACCEPT

# DO NOT REMOVE - Custom Rules
iptables -A INPUT -p tcp -m tcp --dport 80 -j ACCEPT
iptables -A INPUT -p tcp -m tcp --dport 443 -j ACCEPT

iptables -P INPUT DROP
        ');

        $this->remoteTaskService->run('chmod 775 /etc/opt/iptables');
        $this->remoteTaskService->run('/etc/opt/./iptables');
        $this->remoteTaskService->run('iptables-save > /etc/iptables/rules.v4');
        $this->remoteTaskService->run('ip6tables-save > /etc/iptables/rules.v6');
    }

    public function addFirewallRule(ServerFirewallRule $serverFirewallRule)
    {
        $this->connectToServer();

        if (empty($serverFirewallRule->from_ip)) {
            $this->remoteTaskService->findTextAndAppend(
                '/etc/opt/iptables',
                '# DO NOT REMOVE - Custom Rules',
                "iptables -A INPUT -p tcp -m tcp --dport $serverFirewallRule->port -j ACCEPT"
            );
        } else {
            $this->remoteTaskService->removeLineByText(
                '/etc/opt/iptables',
                "iptables -A INPUT -s $serverFirewallRule->from_ip -p tcp -m tcp --dport $serverFirewallRule->port -j ACCEPT"
            );
        }

        return $this->rebuildFirewall($serverFirewallRule->server);
    }

    public function removeFirewallRule(ServerFirewallRule $firewallRule)
    {
        $this->connectToServer();

        if (empty($firewallRule->from_ip)) {
            $errors = $this->remoteTaskService->removeLineByText('/etc/opt/iptables',
                "iptables -A INPUT -p tcp -m tcp --dport $firewallRule->port -j ACCEPT");
        } else {
            $errors = $this->remoteTaskService->removeLineByText('/etc/opt/iptables',
                "iptables -A INPUT -s $firewallRule->from_ip -p tcp -m tcp --dport $firewallRule->port -j ACCEPT");
        }

        if (empty($errors)) {
            return $this->rebuildFirewall($this->server);
        }

        return $errors;
    }

    public function addServerNetworkRule($serverIP)
    {
        $this->connectToServer();

        $this->remoteTaskService->findTextAndAppend('/etc/opt/iptables', '# DO NOT REMOVE - Custom Rules',
            "iptables -A INPUT -s $serverIP -j ACCEPT");

        return $this->rebuildFirewall();
    }

    public function removeServerNetworkRule($serverIP)
    {
        $this->connectToServer();

        $this->remoteTaskService->removeLineByText('/etc/opt/iptables', "iptables -A INPUT -s $serverIP -j ACCEPT");

        return $this->rebuildFirewall();
    }

    private function rebuildFirewall()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('/etc/opt/./iptables');
        $this->remoteTaskService->run('iptables-save > /etc/iptables/rules.v4');
        $this->remoteTaskService->run('ip6tables-save > /etc/iptables/rules.v6');

        return $this->remoteTaskService->getErrors();
    }
}
