<?php

namespace App\Services\Systems\Ubuntu\V_16_04;

use App\Models\Daemon;
use App\Models\Worker;
use App\Services\Systems\SystemService;
use App\Services\Systems\ServiceConstructorTrait;

class WorkerService
{
    use ServiceConstructorTrait;

    /**
     * @description Beanstalk is a simple, fast work queue.
     */
    public function installBeanstalk()
    {
        $this->connectToServer();

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y beanstalkd');
        $this->remoteTaskService->updateText('/etc/default/beanstalkd', 'BEANSTALKD_LISTEN_ADDR', 'BEANSTALKD_LISTEN_ADDR=0.0.0.0');
        $this->remoteTaskService->updateText('/etc/default/beanstalkd', '#BEANSTALKD_EXTRA=', 'BEANSTALKD_EXTRA="-b /var/lib/beanstalkd"');
        $this->remoteTaskService->run('service beanstalkd restart');

        $this->addToServiceRestartGroup(SystemService::WORKER_SERVICE_GROUP, 'service beanstalkd restart');
    }

    /**
     * @description Supervisor is a process control system
     */
    public function installSupervisor()
    {
        // TODO - open port
        $this->connectToServer();

        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y supervisor');
        $this->remoteTaskService->run('mkdir /home/codepier/workers');

        $this->remoteTaskService->run('systemctl enable supervisor');
        $this->remoteTaskService->run('service supervisor start');

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

        $this->remoteTaskService->run('supervisorctl reread');
        $this->remoteTaskService->run('supervisorctl update');
        $this->remoteTaskService->run('supervisorctl start server-worker-'.$worker->id.':*');
    }

    public function removeWorker(Worker $worker, $user = 'root')
    {
        $this->connectToServer($user);

        $this->remoteTaskService->run('rm /etc/supervisor/conf.d/server-worker-'.$worker->id.'.conf');

        $this->remoteTaskService->run('supervisorctl reread');
        $this->remoteTaskService->run('supervisorctl update');
    }

    public function addDaemon(Daemon $daemon, $sshUser = 'root')
    {
        $this->connectToServer($sshUser);

        $this->remoteTaskService->writeToFile('/etc/supervisor/conf.d/server-daemon-'.$daemon->id.'.conf ', '
[program:server-worker-'.$daemon->id.']
process_name=%(program_name)s_%(process_num)02d
command='.$daemon->command.'
autostart=true
autorestart=true
user='.$daemon->user.'
numprocs=1
redirect_stderr=true
stdout_logfile=/home/codepier/workers/server-worker-'.$daemon->id.'.log
');

        $this->remoteTaskService->run('supervisorctl reread');
        $this->remoteTaskService->run('supervisorctl update');
        $this->remoteTaskService->run('supervisorctl start server-daemon-'.$daemon->id.':*');
    }

    public function removeDaemon(Daemon $daemon, $user = 'root')
    {
        $this->connectToServer($user);

        $this->remoteTaskService->run('rm /etc/supervisor/conf.d/server-daemon-'.$daemon->id.'.conf');

        $this->remoteTaskService->run('supervisorctl reread');
        $this->remoteTaskService->run('supervisorctl update');
    }
}
