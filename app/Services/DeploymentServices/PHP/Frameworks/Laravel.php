<?php

namespace App\Services\DeploymentServices\PHP\Frameworks;

trait Laravel
{
    /**
     * @description Creates a symbolic link for the env file
     *
     * @zero_downtime_deployment
     *
     * @order 210
     */
    public function laravelCreateSymbolicEnv()
    {
        $this->remoteTaskService->ssh($this->server, 'codepier');

        $output = [];

        if ($this->remoteTaskService->isFileEmpty($this->siteFolder.'/.env') && $this->remoteTaskService->hasFile($this->release.'/.env.example')) {
            $output = $this->remoteTaskService->run('cp '.$this->release.'/.env.example '.$this->siteFolder.'/.env');
        }

        $appKey = str_after($this->remoteTaskService->getFileLine($this->siteFolder.'/.env', '^APP_KEY='), '=');

        if ($this->zeroDowntimeDeployment) {
            $output = $this->remoteTaskService->run('ln -sfn '.$this->siteFolder.'/.env '.$this->release.'/.env');
        }

        if (empty($appKey)) {
            $output = $this->remoteTaskService->run('cd '.$this->release.'; php artisan key:generate');
        }

        return $output;
    }

    /**
     * @description Creates a symbolic link for the storage folder so it retains the storage files
     *
     * @zero_downtime_deployment
     *
     * @order 150
     */
    public function laravelCreateSymbolicStorageFolder()
    {
        $this->remoteTaskService->ssh($this->server, 'codepier');

        $output = [];

        if ($this->zeroDowntimeDeployment) {
            $this->remoteTaskService->run('cp -r '.$this->release.'/storage '.$this->siteFolder);
            $this->remoteTaskService->run('rm '.$this->release.'/storage -rf');
            $this->remoteTaskService->run('ln -sfn '.$this->siteFolder.'/storage '.$this->release);
        }

        return $output;
    }

    /**
     * @description Creates a symbolic link for the storage folder to the public directory
     *
     * @order 220
     */
    public function laravelMapStorageFolderToPublic()
    {
        $this->remoteTaskService->ssh($this->server, 'codepier');

        return $this->remoteTaskService->run('cd '.$this->release.'; php artisan storage:link');
    }

    /**
     * @description Runs the migrations
     *
     * @order 320
     */
    public function laravelRunMigrations()
    {
        $this->remoteTaskService->ssh($this->server, 'codepier');

        return $this->remoteTaskService->run('cd '.$this->release.'; php artisan migrate --force --no-interaction');
    }

    /**
     * @description Caches the Laravel Routes
     *
     * @order 310
     */
    public function laravelCacheRoutes()
    {
        $this->remoteTaskService->ssh($this->server, 'codepier');

        return $this->remoteTaskService->run('cd '.$this->release.'; php artisan route:cache');
    }

    /**
     * @description Caches Laravel Configs
     *
     * @order 300
     */
    public function laravelCacheConfig()
    {
        $this->remoteTaskService->ssh($this->server, 'codepier');

        return $this->remoteTaskService->run('cd '.$this->release.'; php artisan config:cache');
    }

    /**
     * @description Restarts Laravel Horizon Process
     *
     * @order 400
     *
     * @not_default
     */
    public function laravelTerminateRestartHorizon()
    {
        $this->remoteTaskService->ssh($this->server, 'codepier');

        return $this->remoteTaskService->run('cd '.$this->release.'; php artisan horizon:terminate');
    }

    /**
     * @description Restarts any queue workers
     *
     * @order 410
     *
     * @not_default
     */
    public function laravelRestartWorkers()
    {
        $this->remoteTaskService->ssh($this->server, 'codepier');

        return $this->remoteTaskService->run('cd '.$this->release.'; php artisan queue:restart');
    }
}
