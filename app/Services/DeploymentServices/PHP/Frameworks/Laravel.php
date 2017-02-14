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
        $output = [];
        $output[] = $this->remoteTaskService->run('([ -f '.$this->siteFolder.'/.env ]) || cat '.$this->release.'/.env.example >> '.$this->siteFolder.'/.env');
        $output[] = $this->remoteTaskService->run('ln -sfn '.$this->siteFolder.'/.env '.$this->release.'/.env');

        return $output;
    }

    /**
     * @description Creates a symbolic link for the storage folder so it retains the storage files
     * @order 150
     */
    public function laravelCreateSymbolicStorageFolder()
    {
        $output = [];
        $output[] = $this->remoteTaskService->run('([ -d '.$this->siteFolder.'/storage ]) || (mv '.$this->release.'/storage '.$this->siteFolder.')');
        $output[] = $this->remoteTaskService->run('rm '.$this->release.'/storage -rf');
        $output[] = $this->remoteTaskService->run('ln -sfn '.$this->siteFolder.'/storage '.$this->release);

        return $output;
    }

    /**
     * @description Creates a symbolic link for the storage folder to the public directory
     * @order 205
     */
    public function laravelMapStorageFolderToPublic()
    {
        return [$this->remoteTaskService->run('cd '.$this->release.'; php artisan storage:link')];
    }

    /**
     * @description Runs the migrations
     *
     * @order 210
     *
     * @return [
     */
    public function laravelRunMigrations()
    {
        return [$this->remoteTaskService->run('cd '.$this->release.'; php artisan migrate --force --no-interaction')];
    }

    /**
     * @description Caches the Laravel Routes
     *
     * @order 310
     *
     * @return [
     */
    public function laravelCacheRoutes()
    {
        return [$this->remoteTaskService->run('cd '.$this->release.'; php artisan route:cache')];
    }

    /**
     * @description Caches Laravel Configs
     *
     * @order 320
     *
     * @return [
     */
    public function laravelCacheConfig()
    {
        return [$this->remoteTaskService->run('cd '.$this->release.'; php artisan config:cache')];
    }

    /**
     * @description Restarts any queue workers
     *
     * @order 410
     *
     * @return [
     */
    public function laravelRestartWorkers()
    {
        return [$this->remoteTaskService->run('cd '.$this->release.'; php artisan queue:restart')];
    }
}
