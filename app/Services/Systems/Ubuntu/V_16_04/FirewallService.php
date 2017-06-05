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

        $this->remoteTaskService->writeToFile('/etc/opt/iptables', "
    #!/bin/sh
    iptables --wait --flush
    iptables --wait --delete-chain
    iptables --wait --table nat --flush
    iptables --wait --table nat --delete-chain
    iptables --wait --table mangle --flush
    iptables --wait --table mangle --delete-chain
    
    # default accept
    iptables --wait --policy FORWARD ACCEPT
    iptables --wait --policy OUTPUT ACCEPT
    
    # allow loopback device (127.0.0.1)
    iptables --wait --append INPUT --in-interface lo --jump ACCEPT
    
    # SSH
    iptables --wait --append INPUT --policy tcp --match tcp --dport ".$this->server->port." --jump ACCEPT
    
    # DO NOT REMOVE - Custom Rules
    
    # default drop
    iptables --wait --policy INPUT DROP
");

        $this->remoteTaskService->run('chmod 775 /etc/opt/iptables');
        $this->rebuildFirewall();
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
                $rule = "iptables --wait --append INPUT -s $firewallRule->from_ip --policy tcp --match tcp --dport $firewallRule->port --jump ACCEPT";
            } else {
                $rule = "iptables --wait --append INPUT --policy tcp --match tcp --dport $firewallRule->port --jump ACCEPT";
            }
        } else {
            $rule = "iptables --wait --in-interface INPUT --policy tcp -s $firewallRule->from_ip --jump ACCEPT";
        }

        return $rule;
    }

    private function rebuildFirewall()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('/etc/opt/./iptables');
        $this->remoteTaskService->run('netfilter-persistent save');
        $this->remoteTaskService->run('netfilter-persistent reload');

        return $this->remoteTaskService->getErrors();
    }
}
