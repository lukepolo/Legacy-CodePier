<?php

namespace App\Services\Server\ProvisionSystems;

class DaemonService
{
    public function installSupervisor()
    {
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y supervisor');
        $this->remoteTaskService->run('mkdir /home/codepier/workers');

        $this->remoteTaskService->run('service supervisor start');
    }

    public function installBeanstalk()
    {
        $this->remoteTaskService->run('DEBIAN_FRONTEND=noninteractive apt-get install -y beanstalkd');
        $this->remoteTaskService->run('sed -i "s/#START=yes/START=yes/" /etc/default/beanstalkd');
        $this->remoteTaskService->run('service beanstalkd restart');
    }

    /**
     * @param ServerDaemon $serverDaemon
     * @param string $sshUser
     * @return array
     */
    public function installDaemon(ServerDaemon $serverDaemon, $sshUser = 'root')
    {
        $this->remoteTaskService->ssh($serverDaemon->server, $sshUser);

        $this->remoteTaskService->writeToFile('/etc/supervisor/conf.d/server-worker-' . $serverDaemon->id . '.conf ', '
[program:server-worker-' . $serverDaemon->id . ']
process_name=%(program_name)s_%(process_num)02d
command=' . $serverDaemon->command . '
autostart=' . ($serverDaemon->auto_start ? "true" : "false") . '
autorestart=' . ($serverDaemon->auto_restart ? "true" : "false") . '
user=' . $serverDaemon->user . '
numprocs=' . $serverDaemon->number_of_workers . '
redirect_stderr=true
stdout_logfile=/home/codepier/workers/server-worker-' . $serverDaemon->id . '.log
');

        $this->remoteTaskService->run('supervisorctl reread');
        $this->remoteTaskService->run('supervisorctl update');
        $this->remoteTaskService->run('supervisorctl start server-worker-' . $serverDaemon->id . ':*');

        return $this->remoteTaskService->getErrors();
    }

    /**
     * @param ServerDaemon $serverDaemon
     * @param string $user
     * @return array|bool
     */
    public function removeDaemon(ServerDaemon $serverDaemon, $user = 'root')
    {
        $this->remoteTaskService->ssh($serverDaemon->server, $user);

        $this->remoteTaskService->run('rm /etc/supervisor/conf.d/server-worker-' . $serverDaemon->id . '.conf');

        $this->remoteTaskService->run('supervisorctl reread');
        $this->remoteTaskService->run('supervisorctl update');


        $errors = $this->remoteTaskService->getErrors();

        if (empty($errors)) {
            $serverDaemon->delete();
            return true;
        }

        return $errors;
    }

    /**
     * @param Server $server
     * @param string $user
     * @return bool
     */
    public function restartWorkers(Server $server, $user = 'root')
    {
        $this->remoteTaskService->ssh($server, $user);

        return $this->remoteTaskService->run('supervisorctl restart all');
    }
}