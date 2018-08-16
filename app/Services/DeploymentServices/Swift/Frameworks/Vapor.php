<?php

namespace App\Services\DeploymentServices\Swift\Frameworks;

use App\Repositories\WorkerRepository;

trait Vapor
{
    /**
     * @description Some Command
     *
     * @order 210
     */
    public function vaporUpdate()
    {
        $this->remoteTaskService->ssh($this->server, 'codepier');
        return [$this->remoteTaskService->run("cd $this->release; vapor update -y")];
    }


//    /** @var WorkerRepository  */
//    protected $workerRepository;
//
//    /**
//     * Swift constructor.
//     * @param WorkerRepository $workerRepository
//     */
//    public function __construct(WorkerRepository $workerRepository)
//    {
//        $this->workerRepository = $workerRepository;
//    }
//
    /**
     * @description Vapor Build
     *
     * @order 250
     */
    public function vaporBuild()
    {
        $this->remoteTaskService->ssh($this->server, 'codepier');

        $output[] = $this->remoteTaskService->run("cd $this->release; vapor build --release");
        $output[] = $this->remoteTaskService->run("mv $this->release/.build/release/Run $this->release/.build/release/Run-{$this->site->id}");

        $workerRepository = app(WorkerRepository::class);

        // TODO - use $site->port eventually
        $command = "$this->release/.build/release/Run-{$this->site->id} --port 8080";
        if ($this->site->workers()->where(
            'command', $command
        )->get()->isEmpty()) {
            $workerRepository->create(
                $this->site,
                true,
                true,
                'codepier',
                $command,
                1,
                "$this->release"
            );
        }

        return $output;
    }

    /**
     * @description Kill Vapor
     *
     * @order 300
     */
    public function vaporKillRunningProcess()
    {
        $this->remoteTaskService->ssh($this->server, 'codepier');
        return [$this->remoteTaskService->run("killall Run-{$this->site->id}")];
    }
}
