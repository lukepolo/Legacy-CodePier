<?php

namespace App\Services\Systems\Ubuntu\V_18_04;

use App\Models\CronJob;
use App\Models\Server\Server;
use App\Services\Systems\ServiceConstructorTrait;

class MonitoringService
{
    use ServiceConstructorTrait;

    const DISK_USAGE_SCRIPT = 'sleep $((RANDOM % 250)) && df -m / | grep / | awk \'{ print "disk="$1 " used="$3 " available="$4 " percent="$5}\'';
    const MEMORY_SCRIPT = 'sleep $((RANDOM % 250)) && free -m | grep : | awk \'{ print "name="$1 " total="$2 " used="$3 " free="$4 " available="$7}\'';
    const LOAD_AVG_SCRIPT = 'sleep $((RANDOM % 250)) && cpus=$(cat /proc/cpuinfo | grep processor | wc -l) && current_load=$(cat /proc/loadavg | grep / | awk \'{ print "1="$1 " 5="$2 " 15="$3}\')';

    /**
     *  @name Disk Monitoring Script
     *  @description This script monitors your disk usage and will notify you if your disks are getting full.
     */
    public function installDiskMonitoringScript()
    {
        $this->connectToServer();

        $this->remoteTaskService->writeToFile('/opt/codepier/diskusage_monitor', '
' . self::DISK_USAGE_SCRIPT . ' | while read -r disk_usage;  do
    curl "' . config('app.url_stats') . '/webhook/diskusage/' . $this->server->encode() . '?disk_usage=$disk_usage"
done');

        $this->remoteTaskService->run('chmod 775 /opt/codepier/diskusage_monitor');

        $cronJob = '*/5 * * * * /opt/codepier/./diskusage_monitor';

        $this->remoteTaskService->run('crontab -l | (grep ' . $cronJob . ') || ((crontab -l; echo "' . $cronJob . ' > /dev/null 2>&1") | crontab)');

        $this->createCronJob($this->server, $cronJob, 'root');
    }

    /**
     *  @name Server Load Monitoring Script
     *  @description This script monitors your load across your cores, it will notify you if your load activity is abnormally high.
     */
    public function installLoadMonitoringScript()
    {
        $this->connectToServer();

        // Loads are 1m 5m 15m
        $this->remoteTaskService->writeToFile('/opt/codepier/load_monitor', '
    ' . self::LOAD_AVG_SCRIPT . '
    curl "' . config('app.url_stats') . '/webhook/loads/' . $this->server->encode() . '?loads=$current_load&cpus=$cpus"
');

        $this->remoteTaskService->run('chmod 775 /opt/codepier/load_monitor');

        $cronJob = '*/5 * * * * /opt/codepier/./load_monitor';

        $this->remoteTaskService->run('crontab -l | (grep ' . $cronJob . ') || ((crontab -l; echo "' . $cronJob . ' > /dev/null 2>&1") | crontab)');

        $this->createCronJob($this->server, $cronJob, 'root');
    }

    /**
     *  @name Memory Monitoring Script
     *  @description This script monitors your memory usage and will notify you if your available memory is getting low
     */
    public function installServerMemoryMonitoringScript()
    {
        $this->connectToServer();

        $this->remoteTaskService->writeToFile('/opt/codepier/memory_monitor', '
' . self::MEMORY_SCRIPT . ' | while read -r current_memory; do
    curl "' . config('app.url_stats') . '/webhook/memory/' . $this->server->encode() . '?memory=$current_memory"
done');

        $this->remoteTaskService->run('chmod 775 /opt/codepier/memory_monitor');

        $cronJob = '*/5 * * * * /opt/codepier/./memory_monitor';

        $this->remoteTaskService->run('crontab -l | (grep ' . $cronJob . ') || ((crontab -l; echo "' . $cronJob . ' > /dev/null 2>&1") | crontab)');

        $this->createCronJob($this->server, $cronJob, 'root');
    }

    /**
     *  @name Schema Backup Script
     *  @description This script monitors will trigger backup events.
     */
    public function installSchemaBackupScript()
    {
        $this->connectToServer();

        $this->remoteTaskService->writeToFile('/opt/codepier/schema_backup', 'sleep $((RANDOM % 250)) && curl "' . config('app.public_url') . '/webhook/schema-backups/' . $this->server->encode() . '"');

        $this->remoteTaskService->run('chmod 775 /opt/codepier/schema_backup');

        $cronJob = '0 0 * * * /opt/codepier/./schema_backup';

        $this->remoteTaskService->run('crontab -l | (grep ' . $cronJob . ') || ((crontab -l; echo "' . $cronJob . ' > /dev/null 2>&1") | crontab)');

        $this->createCronJob($this->server, $cronJob, 'root');
    }

    private function createCronJob(Server $server, $cronJob, $user)
    {
        $cronJob = new CronJob([
            'job'       => $cronJob,
            'user'      => $user,
        ]);

        $server->cronJobs()->save($cronJob);
    }
}
