<?php

namespace App\Services\Repository\Providers;

use App\Models\Site\Site;
use App\Models\User\UserRepositoryProvider;

interface RepositoryContract
{
    /**
     * Sets the token so we can connect to the users account.
     * @param \App\Models\User\UserRepositoryProvider $userRepositoryProvider
     * @throws \Exception
     */
    public function setToken(UserRepositoryProvider $userRepositoryProvider);

    /**
     * Gets the users repositories username.
     * @param $repository
     * @return mixed
     */
    public function getRepositoryUser($repository);

    /**
     * Gets the users repositories slug.
     * @param $repository
     * @return mixed
     */
    public function getRepositorySlug($repository);

    /**
     * Creates a webhook based on the site.
     * @param Site $site
     * @return mixed
     */
    public function createDeployHook(Site $site);

    /**
     * Deletes a web hook based on the site.
     * @param Site $site
     * @return mixed
     */
    public function deleteDeployHook(Site $site);

    /**
     * @param UserRepositoryProvider $userRepositoryProvider
     * @return string
     */
    public function getToken(UserRepositoryProvider $userRepositoryProvider);
}
