<?php

namespace App\Services\Systems\Ubuntu\V_16_04;

use App\Models\Worker;
use App\Services\AbstractService;
use App\Services\Systems\SystemService;

class WorkerService extends AbstractService
{
    /**
     * @description Beanstalk is a simple, fast work queue.
     */
    public function installBeanstalk()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y beanstalkd');
        $this->remoteTaskService->updateText('/etc/default/beanstalkd', '#START=yes', 'START=yes');
        $this->remoteTaskService->run('service beanstalkd restart');

        $this->addToServiceRestartGroup(SystemService::WORKER_SERVICE_GROUP, 'service beanstalkd restart');
    }

    /**
     * @description Supervisor is a process control system
     */
    public function installSupervisor()
    {
        $this->connectToServer();

        $this->remoteTaskService->run([
            'DEBIAN_FRONTEND=noninteractive apt-get install -y supervisor',
            'mkdir /home/codepier/workers',
            'systemctl enable supervisor',
            'service supervisor start',
        ]);

        $this->addToServiceRestartGroup(SystemService::WORKER_SERVICE_GROUP, 'service supervisor restart');
    }

    public function addWorker(Worker $worker, $sshUser = 'root')
    {
        $this->connectToServer($sshUser);

        $this->remoteTaskService->writeToFile('/etc/supervisor/conf.d/server-worker-'.$worker->id.'.conf ', '
[program:server-worker-'.$worker->id.']
process_name=%(program_name)s_%(process_num)02d
command='.$worker->command.'
autostart='.($worker->auto_start ? 'true' : 'false').'
autorestart='.($worker->auto_restart ? 'true' : 'false').'
user='.$worker->user.'
numprocs='.$worker->number_of_workers.'
redirect_stderr=true
stdout_logfile=/home/codepier/workers/server-worker-'.$worker->id.'.log
');

        $this->remoteTaskService->run([
            'supervisorctl reread',
            'supervisorctl update',
            'supervisorctl start server-worker-'.$worker->id.':*',
        ]);
    }

    public function removeWorker(Worker $worker, $user = 'root')
    {
        $this->connectToServer($user);

        $this->remoteTaskService->run([
            'rm /etc/supervisor/conf.d/server-worker-'.$worker->id.'.conf',
            'supervisorctl reread',
            'supervisorctl update',
        ]);
    }
}
