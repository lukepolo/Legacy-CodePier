<?php

namespace App\Services\Systems\Ubuntu\V_16_04;

use App\Models\FirewallRule;
use App\Services\AbstractService;
use App\Services\Systems\ServiceConstructorTrait;

class FirewallService extends AbstractService
{

    public function addBasicFirewallRules()
    {
        $this->connectToServer();

        $this->remoteTaskService->run([
            'ufw default deny incoming',
            'ufw default allow ongoing',
            'ufw allow ssh',
            'ufw disable',
            'ufw allow '.$this->server->port.'/tcp',
            'echo "y" | ufw enable'
        ]);
    }

    public function addFirewallRule(FirewallRule $firewallRule)
    {
        $this->connectToServer();

        if (! empty($firewallRule->from_ip)) {
            $command = "ufw allow proto $firewallRule->type from $firewallRule->from_ip to any port $firewallRule->port";
        } else {
            $command = "ufw allow $firewallRule->port/$firewallRule->type";
        }

        return $this->remoteTaskService->run('
for i in {1..5}
do
   count=$(ps -A -ww | grep [^]]ufw | wc -l)
   if [ $count -eq 0 ]
   then
        '.$command.'
      break
   else
      sleep $[ ( $RANDOM % 10 )  + 1 ]s
   fi
done');
    }

    public function removeFirewallRule(FirewallRule $firewallRule)
    {
        $this->connectToServer();

        if ($firewallRule->from_ip) {
            return $this->remoteTaskService->run(
                "ufw delete allow proto $firewallRule->type from $firewallRule->from_ip to any port $firewallRule->port"
            );
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
