<?php

namespace App\Services\Systems\Ubuntu\V_16_04;

use App\Models\Server\ServerCronJob;
use App\Services\Systems\ServiceConstructorTrait;

class MonitoringService
{
    use ServiceConstructorTrait;

    CONST LOAD_AVG_SCRIPT = 'current_load=$(cat /proc/loadavg | grep / | awk \'{ print "1="$1 " 5="$2 " 15="$3}\')';
    CONST MEMORY_SCRIPT = 'free -m -h | grep : | awk \'{ print "name="$1 " total="$2 " used="$3 " free="$4 " available="$7}\'';
    CONST DISK_USAGE_SCRIPT = 'df -h / | grep / | awk \'{ print "disk="$1 " used="$3 " available="$4 " percent="$5}\'';

    public function installDiskMonitoringScript()
    {
        $this->connectToServer();

        $this->remoteTaskService->writeToFile('/opt/codepier/diskusage_monitor', '
'.self::DISK_USAGE_SCRIPT.' | while read -r disk_usage;  do
    curl '.env('APP_URL').'/webhook/diskspace/'.$this->server->encode().'?disk_usage=$disk_usage
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
    '.self::LOAD_AVG_SCRIPT.'
    curl "'.env('APP_URL').'/webhook/load/'.$this->server->encode().'?loads=$current_load"
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
'.self::MEMORY_SCRIPT.' | while read -r current_memory; do
    curl "'.env('APP_URL').'/webhook/memory/'.$this->server->encode().'?memory=$current_memory"
done');

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

        $observables = $serverCronJob->getObservableEvents();

        $serverCronJob->flushEventListeners();

        $serverCronJob->save();

        $serverCronJob->addObservableEvents($observables);
    }
}
