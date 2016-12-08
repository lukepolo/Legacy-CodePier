<?php

namespace App\Services\Systems\Ubuntu\V_16_04;

use App\Models\Server\ServerCronJob;
use App\Services\Systems\ServiceConstructorTrait;

class MonitoringService
{
    use ServiceConstructorTrait;

    public function installDiskMonitoringScript()
    {
        $this->connectToServer();

        $this->remoteTaskService->writeToFile('/opt/codepier/diskusage_monitor', '
df / | grep / | awk \'{ print $5 " " $6 }\' | while read output;
do
    usep=$(echo $output | awk \'{ print $1}\' | cut -d\'%\' -f1 )
    if [ $usep -ge 90 ]; then
        curl '.env('APP_URL').'/webhook/diskspace?key='.$this->server->encode().'
    fi
done');

        $this->remoteTaskService->run('chmod 775 /opt/codepier/diskusage_monitor');

        $cronJob = '*/5 * * * * /opt/codepier/./diskusage_monitor';

        $this->remoteTaskService->run('crontab -l | (grep '.$cronJob.') || ((crontab -l; echo "'.$cronJob.' >/dev/null 2>&1") | crontab)');

        $this->createCronJob($this->server->id, $cronJob, 'root');

    }

    public function installLoadMonitoringScript()
    {
        $this->connectToServer();

        // Loads are 1m 5m 15m
        $this->remoteTaskService->writeToFile('/opt/codepier/load_monitor', '
    current_load=$(cat /proc/loadavg | grep / | awk \'{ print $1 " " $2 " " $3}\')
    curl "'.env('APP_URL').'/webhook/load?key='.$this->server->encode().'&loads=$current_load"
');

        $this->remoteTaskService->run('chmod 775 /opt/codepier/load_monitor');

        $cronJob = '*/5 * * * * /opt/codepier/./load_monitor';

        $this->remoteTaskService->run('crontab -l | (grep '.$cronJob.') || ((crontab -l; echo "'.$cronJob.' >/dev/null 2>&1") | crontab)');

        $this->createCronJob($this->server->id, $cronJob, 'root');
    }

    public function installServerMemoryMonitoringScript()
    {
        $this->connectToServer();

        $this->remoteTaskService->writeToFile('/opt/codepier/memory_monitor', '
    current_memory=$(free -m | grep Mem | awk \'{ print $2 " " $3 " " $4 " " $7}\')
    curl "'.env('APP_URL').'/webhook/memory?key='.$this->server->encode().'&memory=$current_memory"
');

        $this->remoteTaskService->run('chmod 775 /opt/codepier/memory_monitor');

        $cronJob = '*/5 * * * * /opt/codepier/./memory_monitor';

        $this->remoteTaskService->run('crontab -l | (grep '.$cronJob.') || ((crontab -l; echo "'.$cronJob.' >/dev/null 2>&1") | crontab)');

        $this->createCronJob($this->server->id, $cronJob, 'root');
    }

    private function createCronJob($serverId, $cronJob, $user)
    {
        $serverCronJob = new ServerCronJob([
            'server_id' => $serverId,
            'job'       => $cronJob,
            'user'      => $user,
        ]);

        $serverCronJob->flushEventListeners();

        $serverCronJob->save();
    }
}
