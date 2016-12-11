<?php

namespace App\Services\Repository\Providers;

use App\Models\Site\Site;
use App\Models\User\UserRepositoryProvider;

interface RepositoryContract
{
    /**
     * Imports a deploy key so we can clone the repositories.
     *
     * @param \App\Models\User\UserRepositoryProvider $userRepositoryProvider
     * @param Site $site
     * @param $sshKey
     * @return
     */
    public function importSshKeyIfPrivate(UserRepositoryProvider $userRepositoryProvider, Site $site, $sshKey);

    /**
     * Sets the token so we can connect to the users account.
     *
     * @param \App\Models\User\UserRepositoryProvider $userRepositoryProvider
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function setToken(UserRepositoryProvider $userRepositoryProvider);

    /**
     * Gets the users repositories username.
     *
     * @param $repository
     *
     * @return mixed
     */
    public function getRepositoryUser($repository);

    /**
     * Gets the users repositories slug.
     *
     * @param $repository
     *
     * @return mixed
     */
    public function getRepositorySlug($repository);
}
