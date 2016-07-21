<?php

namespace App\Services\Server\Site\Repository\Providers;

use App\Models\UserRepositoryProvider;

/**
 * Interface RepositoryContract
 * @package App\Services\Server\Site\Repository\Providers
 */
interface RepositoryContract
{
    /**
     * Gets all the repositories for a user
     *
     * @param UserRepositoryProvider $userRepositoryProvider
     * @return mixed
     * @throws \Exception
     */
    public function getRepositories(UserRepositoryProvider $userRepositoryProvider);

    /**
     * Imports a deploy key so we can clone the repositories
     *
     * @param UserRepositoryProvider $userRepositoryProvider
     * @param $repository
     * @param $sshKey
     * @throws \Exception
     */
    public function importSshKey(UserRepositoryProvider $userRepositoryProvider, $repository, $sshKey);

    /**
     * Gets the repository information
     *
     * @param $repository
     * @return mixed
     */
    public function getRepositoryInfo($repository);

    /**
     * Sets the token so we can connect to the users account
     * @param UserRepositoryProvider $userRepositoryProvider
     * @return mixed
     * @throws \Exception
     */
    public function setToken(UserRepositoryProvider $userRepositoryProvider);

    /**
     * Gets the users repositories username
     * @param $repository
     * @return mixed
     */
    public function getRepositoryUser($repository);

    /**
     * Gets the users repositories name
     * @param $repository
     * @return mixed
     */
    public function getRepositoryName($repository);
}