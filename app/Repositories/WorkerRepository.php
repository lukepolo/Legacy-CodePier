<?php

namespace App\Repositories;

use App\Models\Worker;
use App\Models\Site\Site;
use App\Models\Server\Server;
use App\Events\Site\SiteWorkerCreated;
use App\Jobs\Server\Workers\InstallServerWorker;

class WorkerRepository
{
    /**
     * @param Server | Site $model
     * @param $autoStart
     * @param $autoRestart
     * @param $user
     * @param $command
     * @param $numberOfWorkers
     * @param $workingDirectory
     * @return mixed
     */
    public function create($model, $autoStart, $autoRestart, $user, $command, $numberOfWorkers, $workingDirectory)
    {
        $worker = Worker::create([
            'auto_start' => $autoStart,
            'auto_restart' => $autoRestart,
            'user' => $user,
            'command' => $command,
            'number_of_workers' => $numberOfWorkers,
            'working_directory' => $workingDirectory,
        ]);

        $model->workers()->save($worker);

        if ($model instanceof Site) {
            event(new SiteWorkerCreated($model, $worker));
        } elseif ($model instanceof Server) {
            dispatch(
                (new InstallServerWorker($model, $worker))
                    ->onQueue(config('queue.channels.server_commands'))
            );
        } else {
            throw new \Error("You passed an invalid model");
        }

        return $worker;
    }


    /**
     * @param Worker $worker
     * @return bool
     * @throws \Exception
     */
    public function destroy(Worker $worker)
    {
        return $worker->delete();
    }
}
