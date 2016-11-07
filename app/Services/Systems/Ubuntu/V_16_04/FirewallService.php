<?php

namespace App\Services\Systems\Ubuntu\V_16_04;

use App\Models\Server\ServerFirewallRule;
use App\Services\Systems\ServiceConstructorTrait;

class FirewallService
{
    const IP_TABLES_FILE = '/opt/codepier/iptables';
    const IP_TABLES_FILE_COMMAND = '/opt/codepier/./iptables';
    use ServiceConstructorTrait;

    public function addBasicFirewallRules()
    {
        $this->connectToServer();

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

        $this->remoteTaskService->writeToFile(self::IP_TABLES_FILE, '
#!/bin/sh

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

iptables -P INPUT DROP
        ');

        $this->remoteTaskService->run('chmod 775 '.self::IP_TABLES_FILE);
        $this->rebuildFirewall();
    }

    public function addFirewallRule(ServerFirewallRule $serverFirewallRule)
    {
        $this->connectToServer();

        if (empty($serverFirewallRule->from_ip)) {
            $this->remoteTaskService->findTextAndAppend(
                '/opt/codepier/iptables',
                '# DO NOT REMOVE - Custom Rules',
                "iptables -A INPUT -p tcp -m tcp --dport $serverFirewallRule->port -j ACCEPT"
            );
        } else {
            $this->remoteTaskService->removeLineByText(
                '/opt/codepier/iptables',
                "iptables -A INPUT -s $serverFirewallRule->from_ip -p tcp -m tcp --dport $serverFirewallRule->port -j ACCEPT"
            );
        }

        return $this->rebuildFirewall();
    }

    public function removeFirewallRule(ServerFirewallRule $firewallRule)
    {
        $this->connectToServer();

        if (empty($firewallRule->from_ip)) {
            $errors = $this->remoteTaskService->removeLineByText(self::IP_TABLES_FILE,
                "iptables -A INPUT -p tcp -m tcp --dport $firewallRule->port -j ACCEPT");
        } else {
            $errors = $this->remoteTaskService->removeLineByText(self::IP_TABLES_FILE,
                "iptables -A INPUT -s $firewallRule->from_ip -p tcp -m tcp --dport $firewallRule->port -j ACCEPT");
        }

        if (empty($errors)) {
            return $this->rebuildFirewall();
        }

        return $errors;
    }

    public function addServerNetworkRule($serverIP)
    {
        $this->connectToServer();

        $this->remoteTaskService->findTextAndAppend(self::IP_TABLES_FILE, '# DO NOT REMOVE - Custom Rules',
            "iptables -A INPUT -s $serverIP -j ACCEPT");

        return $this->rebuildFirewall();
    }

    public function removeServerNetworkRule($serverIP)
    {
        $this->connectToServer();

        $this->remoteTaskService->removeLineByText(self::IP_TABLES_FILE, "iptables -A INPUT -s $serverIP -j ACCEPT");

        return $this->rebuildFirewall();
    }

    private function rebuildFirewall()
    {
        $this->connectToServer();

        $this->remoteTaskService->run(self::IP_TABLES_FILE_COMMAND);

        return $this->remoteTaskService->getErrors();
    }
}
