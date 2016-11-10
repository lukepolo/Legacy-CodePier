<?php

namespace App\Services\DeploymentServices\PHP\Frameworks;

trait Laravel
{
    /**
     * @description Creates a symbolic link for the env file
     * @order 110
     */
    public function laravelCreateSymbolicEnv()
    {
        $output[] = $this->remoteTaskService->run('([ -f '.$this->site_folder.'/.env ]) || cat '.$this->release.'/.env.example >> '.$this->site_folder.'/.env');
        $output[] = $this->remoteTaskService->run('ln -s '.$this->site_folder.'/.env '.$this->release.'/.env');
    }

    /**
     * @description Creates a symbolic link for the storage folder so it retains the storage files
     * @order 150
     */
    public function laravelCreateSymbolicStorageFolder()
    {
        $output[] = $this->remoteTaskService->run('([ -d '.$this->site_folder.'/storage ]) || (mv '.$this->release.'/storage '.$this->site_folder.')');
        $output[] = $this->remoteTaskService->run('rm '.$this->release.'/storage -rf');
        $output[] = $this->remoteTaskService->run('ln -s '.$this->site_folder.'/storage '.$this->release);
    }

    /**
     * @description Runs the migrations
     *
     * @order 210
     *
     * @return array
     */
    public function laravelRunMigrations()
    {
        $output = [];
        $output[] = $this->remoteTaskService->run('cd '.$this->release.'; php artisan migrate --force --no-interaction');

        return $output;
    }

    /**
     * @description Caches the Laravel Routes
     *
     * @order 310
     *
     * @return array
     */
    public function laravelCacheRoutes()
    {
        $output = [];
        $output[] = $this->remoteTaskService->run('cd '.$this->release.'; php artisan route:cache');

        return $output;
    }

    /**
     * @description Caches Laravel Configs
     *
     * @order 320
     *
     * @return array
     */
    public function laravelCacheConfig()
    {
        $output = [];
        $output[] = $this->remoteTaskService->run('cd '.$this->release.'; php artisan config:cache');

        return $output;
    }

    /**
     * @description Restarts any queue workers
     *
     * @order 410
     *
     * @return array
     */
    public function laravelRestartWorkers()
    {
        $output = [];
        $output[] = $this->remoteTaskService->run('cd '.$this->release.'; php artisan queue:restart');

        return $output;
    }
}
