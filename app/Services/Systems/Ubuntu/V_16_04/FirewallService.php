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

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install iptables-persistent -y');

        $this->remoteTaskService->writeToFile('/opt/codepier/iptables', '
    #!/bin/sh
    
    iptables --wait -F
    iptables --wait -X
    iptables --wait -t nat -F
    iptables --wait -t nat -X
    iptables --wait -t mangle -F
    iptables --wait -t mangle -X
    
    iptables --wait -P INPUT ACCEPT
    iptables --wait -P FORWARD ACCEPT
    iptables --wait -P OUTPUT ACCEPT
    
    iptables --wait -A INPUT -m conntrack --ctstate RELATED,ESTABLISHED -j ACCEPT
    iptables --wait -I INPUT 1 -i lo -j ACCEPT
    
    # SSH
    iptables --wait -A INPUT -p tcp -m tcp --dport 22 -j ACCEPT
    
    # DO NOT REMOVE - Custom Rules
    
    iptables --wait -P INPUT DROP
');

        $this->remoteTaskService->run('chmod 775 /opt/codepier/iptables');
        $this->rebuildFirewall();
    }

    public function addFirewallRule(FirewallRule $firewallRule)
    {
        $this->connectToServer();

        $this->remoteTaskService->findTextAndAppend(
            '/opt/codepier/iptables',
            '# DO NOT REMOVE - Custom Rules',
            $this->getRule($firewallRule)
        );

        return $this->rebuildFirewall();
    }

    public function removeFirewallRule(FirewallRule $firewallRule)
    {
        $this->connectToServer();

        $this->remoteTaskService->removeLineByText(
            '/opt/codepier/iptables',
            $this->getRule($firewallRule)
        );

        return $this->rebuildFirewall();
    }

    private function getRule($firewallRule)
    {
        if ($firewallRule->port !== '*') {
            if ($firewallRule->from_ip) {
                $rule = "iptables --wait -A INPUT -s $firewallRule->from_ip -p tcp -m tcp --dport $firewallRule->port -j ACCEPT";
            } else {
                $rule = "iptables --wait -A INPUT -p tcp -m tcp --dport $firewallRule->port -j ACCEPT";
            }
        } else {
            $rule = "iptables --wait -A INPUT -s $firewallRule->from_ip -j ACCEPT";
        }

        return $rule;
    }

    private function rebuildFirewall()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('/opt/codepier/./iptables');
        $this->remoteTaskService->run('netfilter-persistent save');
        $this->remoteTaskService->run('netfilter-persistent reload');

        return $this->remoteTaskService->getErrors();
    }
}
