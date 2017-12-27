<?php

namespace App\Services\DeploymentServices\PHP\Frameworks;

trait Symfony
{

    /**
     * @description Creates a symbolic link for the env file, should create environment variables for production usage
     *
     * @not-default
     * @zerotime-deployment
     *
     * @order 210
     */
    public function symfonyCreateSymbolicEnv()
    {
        if ($this->remoteTaskService->isFileEmpty($this->siteFolder.'/.env') && $this->remoteTaskService->hasFile($this->release.'/.env.dist')) {
            $this->remoteTaskService->run('cp '.$this->release.'/.env.dist '.$this->siteFolder.'/.env');
        }

        if ($this->zerotimeDeployment) {
            $this->remoteTaskService->run('ln -sfn '.$this->siteFolder.'/.env '.$this->release.'/.env');
        }
    }

    /**
     * @description Creates a symbolic link for the logs and sessions folders
     *
     * @zerotime-deployment
     *
     * @order 150
     */
    public function symfonyCreateSymbolicFolders()
    {
        $output = [];

        if ($this->zerotimeDeployment) {
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
     * @not-default
     *
     * @order 325
     */
    public function symfonyDumpAsseticAssets()
    {
        $this->remoteTaskService->run('cd '.$this->release.'; php bin/console assetic:dump --no-debug');
    }


    /**
     * @description Clears the cache.
     *
     * @order 350
     */
    public function symfonyClearCache()
    {
        $this->remoteTaskService->run('cd '.$this->release.'; php bin/console cache:clear --no-debug --no-warmup');
    }


    /**
     * @description Warms up the cache.
     *
     * @order 375
     */
    public function symfonyWarmupCache()
    {
        $this->remoteTaskService->run('cd '.$this->release.'; php bin/console cache:warmup');
    }

    /**
     * @description Runs the migrations
     *
     * @order 230
     */
    public function symfonyRunMigrations()
    {
        return $this->remoteTaskService->run('cd '.$this->release.'; php bin/console doctrine:migrations:migrate --allow-no-migration');
    }
}
