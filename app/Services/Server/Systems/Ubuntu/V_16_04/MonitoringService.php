<?php

namespace App\Services\Server\Systems\Ubuntu\V_16_04;

use App\Services\Server\Systems\Traits\ServiceConstructorTrait;

class MonitoringService
{
    use ServiceConstructorTrait;

    public function addDiskMonitoringScript()
    {
        $this->remoteTaskService->writeToFile('/etc/opt/diskusage','
df / | grep / | awk \'{ print $5 " " $6 }\' | while read output;
do
    usep=$(echo $output | awk \'{ print $1}\' | cut -d\'%\' -f1 )
    if [ $usep -ge 90 ]; then
        curl '.env('APP_URL').'/webhook/diskspace?key='.$this->server->encode().'
    fi
done');

        $this->remoteTaskService->run('chmod 775 /etc/opt/diskusage');

        $cronJob = '*/5 * * * * /etc/opt/./diskusage';

        $this->remoteTaskService->run('crontab -l | (grep ' . $cronJob . ') || ((crontab -l; echo "' . $cronJob . ' >/dev/null 2>&1") | crontab)');
    }
}