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

        $this->remoteTaskService->writeToFile('/etc/opt/iptables', "
    #!/bin/sh
    echo 'REDOING IP TABLES'
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
    iptables -A INPUT -p tcp -m tcp --dport ".$this->server->port.' -j ACCEPT
    
    # DO NOT REMOVE - Custom Rules
    
    iptables -P INPUT DROP
');

        $this->remoteTaskService->run('chmod 775 /etc/opt/iptables');
        $this->remoteTaskService->run('/etc/opt/./iptables');
        $this->remoteTaskService->run('iptables-save > /etc/iptables/rules.v4');
        $this->remoteTaskService->run('ip6tables-save > /etc/iptables/rules.v6');
    }

    public function addFirewallRule(FirewallRule $firewallRule)
    {
        $this->connectToServer();

        $this->remoteTaskService->findTextAndAppend(
            '/etc/opt/iptables',
            '# DO NOT REMOVE - Custom Rules',
            $this->getRule($firewallRule)
        );

        return $this->rebuildFirewall();
    }

    public function removeFirewallRule(FirewallRule $firewallRule)
    {
        $this->connectToServer();

        $this->remoteTaskService->removeLineByText(
            '/etc/opt/iptables',
            $this->getRule($firewallRule)
        );

        return $this->rebuildFirewall();
    }

    private function getRule($firewallRule)
    {
        if ($firewallRule->port !== '*') {
            if ($firewallRule->from_ip) {
                $rule = "iptables -A INPUT -s $firewallRule->from_ip -p tcp -m tcp --dport $firewallRule->port -j ACCEPT";
            } else {
                $rule = "iptables -A INPUT -p tcp -m tcp --dport $firewallRule->port -j ACCEPT";
            }
        } else {
            $rule = "iptables -I INPUT -p tcp -s $firewallRule->from_ip -j ACCEPT";
        }

        return $rule;
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
