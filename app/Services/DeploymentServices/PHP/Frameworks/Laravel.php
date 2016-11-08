<?php

namespace App\Services\DeploymentServices\PHP\Frameworks;

class Laravel
{

    /**
     * @description Creates a symbolic link for the storage folder so it retains the storage files
     * @order 110
     */
    public function createSymbolicStorageFolder()
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
    public function runMigrations()
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
    public function cacheRoutes()
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
    public function cacheConfig()
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
    public function restartWorkers()
    {
        $output = [];
        $output[] = $this->remoteTaskService->run('cd '.$this->release.'; php artisan queue:restart');

        return $output;
    }
}
