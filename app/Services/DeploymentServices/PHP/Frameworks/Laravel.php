<?php

namespace App\Services\DeploymentServices\PHP\Frameworks;

class Laravel
{
    public function createSymbolicStorageFolder()
    {
        $output[] = $this->remoteTaskService->run('([ -d '.$this->site_folder.'/storage ]) || (mv '.$this->release.'/storage '.$this->site_folder.')');
        $output[] = $this->remoteTaskService->run('rm '.$this->release.'/storage -rf');
        $output[] = $this->remoteTaskService->run('ln -s '.$this->site_folder.'/storage '.$this->release);
    }

    public function runMigrations()
    {
        $output = [];
        $output[] = $this->remoteTaskService->run('cd '.$this->release.'; php artisan migrate --force --no-interaction');

        return $output;
    }

    public function cacheRoutes()
    {
        $output = [];
        $output[] = $this->remoteTaskService->run('cd '.$this->release.'; php artisan route:cache');

        return $output;
    }

    public function cacheConfig()
    {
        $output = [];
        $output[] = $this->remoteTaskService->run('cd '.$this->release.'; php artisan config:cache');

        return $output;
    }

    public function restartWorkers()
    {
        $output = [];
        $output[] = $this->remoteTaskService->run('cd '.$this->release.'; php artisan queue:restart');

        return $output;
    }
}
