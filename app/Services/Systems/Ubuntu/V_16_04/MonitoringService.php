<?php

namespace App\Services\Systems\Ubuntu\V_16_04;

use App\Models\ServerCronJob;
use App\Services\Systems\ServiceConstructorTrait;

class MonitoringService
{
    use ServiceConstructorTrait;

    public function installDiskMonitoringScript()
    {
        $this->connectToServer();

        $this->remoteTaskService->writeToFile('/opt/codepier/diskusage', '
df / | grep / | awk \'{ print $5 " " $6 }\' | while read output;
do
    usep=$(echo $output | awk \'{ print $1}\' | cut -d\'%\' -f1 )
    if [ $usep -ge 90 ]; then
        curl '.env('APP_URL').'/webhook/diskspace?key='.$this->server->encode().'
    fi
done');

        $this->remoteTaskService->run('chmod 775 /opt/codepier/diskusage');

        $cronJob = '*/5 * * * * /opt/codepier/./diskusage';

        $this->remoteTaskService->run('crontab -l | (grep '.$cronJob.') || ((crontab -l; echo "'.$cronJob.' >/dev/null 2>&1") | crontab)');

        ServerCronJob::create([
            'server_id' => $this->server->id,
            'job'       => $cronJob,
            'user'      => 'root',
        ]);
    }
}
