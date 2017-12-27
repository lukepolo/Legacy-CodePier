<?php

namespace App\Services\DeploymentServices\PHP\Frameworks;

trait Laravel
{
    /**
     * @description Creates a symbolic link for the env file
     *
     * @zerotime-deployment
     *
     * @order 210
     */
    public function laravelCreateSymbolicEnv()
    {
        $output = [];

        if ($this->remoteTaskService->isFileEmpty($this->siteFolder.'/.env') && $this->remoteTaskService->hasFile($this->release.'/.env.example')) {
            $output = $this->remoteTaskService->run('cp '.$this->release.'/.env.example '.$this->siteFolder.'/.env');
        }

        $appKey = str_after($this->remoteTaskService->getFileLine($this->siteFolder.'/.env', '^APP_KEY='), '=');

        if ($this->zerotimeDeployment) {
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
     * @zerotime-deployment
     *
     * @order 150
     */
    public function laravelCreateSymbolicStorageFolder()
    {
        $output = [];

        if ($this->zerotimeDeployment) {
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
        return $this->remoteTaskService->run('cd '.$this->release.'; php artisan storage:link');
    }

    /**
     * @description Runs the migrations
     *
     * @order 230
     */
    public function laravelRunMigrations()
    {
        return $this->remoteTaskService->run('cd '.$this->release.'; php artisan migrate --force --no-interaction');
    }

    /**
     * @description Caches the Laravel Routes
     *
     * @order 310
     */
    public function laravelCacheRoutes()
    {
        return $this->remoteTaskService->run('cd '.$this->release.'; php artisan route:cache');
    }

    /**
     * @description Caches Laravel Configs
     *
     * @order 320
     */
    public function laravelCacheConfig()
    {
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
        return $this->remoteTaskService->run('cd '.$this->release.'; php artisan horizon:terminate');
    }

    /**
     * @description Restarts any queue workers
     *
     * @order 410
     */
    public function laravelRestartWorkers()
    {
        return $this->remoteTaskService->run('cd '.$this->release.'; php artisan queue:restart');
    }
}
