<?php

namespace App\Services\Systems\Ubuntu\V_16_04;

use App\Models\ServerWorker;
use App\Services\Systems\ServiceConstructorTrait;
use App\Services\Systems\SystemService;

class WorkerService
{
    use ServiceConstructorTrait;

    /**
     * Example of the description, we need to go through of all of these eventually.
     */
    public function installBeanstalk()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y beanstalkd');
        $this->remoteTaskService->run('sed -i "s/#START=yes/START=yes/" /etc/default/beanstalkd');
        $this->remoteTaskService->run('service beanstalkd restart');

        $this->addToServiceRestartGroup(SystemService::WORKER_SERVICE_GROUP, 'service beanstalkd restart');
    }

    public function installSupervisor()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y supervisor');
        $this->remoteTaskService->run('mkdir /home/codepier/workers');

        $this->remoteTaskService->run('service supervisor start');

        $this->addToServiceRestartGroup(SystemService::WORKER_SERVICE_GROUP, 'service supervisor restart');
    }

    public function addWorker(ServerWorker $serverWorker, $sshUser = 'root')
    {
        $this->connectToServer($sshUser);

        $this->remoteTaskService->writeToFile('/etc/supervisor/conf.d/server-worker-'.$serverWorker->id.'.conf ', '
[program:server-worker-'.$serverWorker->id.']
process_name=%(program_name)s_%(process_num)02d
command='.$serverWorker->command.'
autostart='.($serverWorker->auto_start ? 'true' : 'false').'
autorestart='.($serverWorker->auto_restart ? 'true' : 'false').'
user='.$serverWorker->user.'
numprocs='.$serverWorker->number_of_workers.'
redirect_stderr=true
stdout_logfile=/home/codepier/workers/server-worker-'.$serverWorker->id.'.log
');

        $this->remoteTaskService->run('supervisorctl reread');
        $this->remoteTaskService->run('supervisorctl update');
        $this->remoteTaskService->run('supervisorctl start server-worker-'.$serverWorker->id.':*');
    }

    public function removeWorker(ServerWorker $serverWorker, $user = 'root')
    {
        $this->connectToServer($user);

        $this->remoteTaskService->run('rm /etc/supervisor/conf.d/server-worker-'.$serverWorker->id.'.conf');

        $this->remoteTaskService->run('supervisorctl reread');
        $this->remoteTaskService->run('supervisorctl update');
    }
}
