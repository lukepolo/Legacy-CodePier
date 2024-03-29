<?php

namespace App\Services\DeploymentServices\PHP\Frameworks;

trait Symfony
{
    /**
     * @description Creates a symbolic link for the env file, should create environment variables for production usage
     *
     * @not_default
     * @zero_downtime_deployment
     *
     * @order 210
     */
    public function symfonyCreateSymbolicEnv()
    {
        $this->remoteTaskService->ssh($this->server, 'codepier');

        $output = [];

        if ($this->remoteTaskService->isFileEmpty($this->siteFolder.'/.env') && $this->remoteTaskService->hasFile($this->release.'/.env.dist')) {
            $output[] = $this->remoteTaskService->run('cp '.$this->release.'/.env.dist '.$this->siteFolder.'/.env');
        }

        if ($this->zeroDowntimeDeployment) {
            $output[] = $this->remoteTaskService->run('ln -sfn '.$this->siteFolder.'/.env '.$this->release.'/.env');
        }

        return $output;
    }

    /**
     * @description Creates a symbolic link for the logs and sessions folders
     *
     * @zero_downtime_deployment
     *
     * @order 150
     */
    public function symfonyCreateSymbolicFolders()
    {
        $this->remoteTaskService->ssh($this->server, 'codepier');

        $output = [];

        if ($this->zeroDowntimeDeployment) {
            $output[] = $this->remoteTaskService->run('cp -r '.$this->release.'/var/logs '.$this->siteFolder);
            $output[] = $this->remoteTaskService->run('rm '.$this->release.'/var/logs -rf');
            $output[] = $this->remoteTaskService->run('ln -sfn '.$this->siteFolder.'/var/logs '.$this->release);

            $output[] = $this->remoteTaskService->run('cp -r '.$this->release.'/var/sessions '.$this->siteFolder);
            $output[] = $this->remoteTaskService->run('rm '.$this->release.'/var/sessions -rf');
            $output[] = $this->remoteTaskService->run('ln -sfn '.$this->siteFolder.'/var/sessions '.$this->release);
        }

        return $output;
    }

    /**
     * @description If your using Assetic, you should dump the assets.
     *
     * @not_default
     *
     * @order 325
     */
    public function symfonyDumpAsseticAssets()
    {
        $this->remoteTaskService->ssh($this->server, 'codepier');

        return $this->remoteTaskService->run('cd '.$this->release.'; php bin/console assetic:dump --no-debug');
    }

    /**
     * @description Clears the cache.
     *
     * @order 350
     */
    public function symfonyClearCache()
    {
        $this->remoteTaskService->ssh($this->server, 'codepier');

        return $this->remoteTaskService->run('cd '.$this->release.'; php bin/console cache:clear --no-debug --no-warmup');
    }

    /**
     * @description Warms up the cache.
     *
     * @order 375
     */
    public function symfonyWarmupCache()
    {
        $this->remoteTaskService->ssh($this->server, 'codepier');

        return $this->remoteTaskService->run('cd '.$this->release.'; php bin/console cache:warmup');
    }

    /**
     * @description Runs the migrations
     *
     * @order 230
     */
    public function symfonyRunMigrations()
    {
        $this->remoteTaskService->ssh($this->server, 'codepier');

        return $this->remoteTaskService->run('cd '.$this->release.'; php bin/console doctrine:migrations:migrate --allow-no-migration');
    }
}
