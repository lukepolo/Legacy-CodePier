<?php

namespace App\Services\Repository\Providers;

use App\Models\Site\Site;
use App\Exceptions\DeployKeyAlreadyUsed;

trait RepositoryTrait
{
    /**
     * Gets the users repositories username.
     *
     * @param $repository
     *
     * @return mixed
     */
    public function getRepositoryUser($repository)
    {
        return explode('/', $repository)[0];
    }

    /**
     * Gets the users repositories name.
     *
     * @param $repository
     *
     * @return mixed
     */
    public function getRepositorySlug($repository)
    {
        return explode('/', $repository)[1];
    }

    /**
     * @param Site $site
     * @return string
     */
    public function sshKeyLabel(Site $site)
    {
        return $site->name;
    }

    public function throwKeyAlreadyUsed()
    {
        throw new DeployKeyAlreadyUsed('Key is already being used');
    }
}
