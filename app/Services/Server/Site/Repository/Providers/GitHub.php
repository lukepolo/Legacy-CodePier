<?php

namespace App\Services\Server\Site\Repository\Providers;

use App\Models\User;
use GitHub;

/**
 * Class GitHub
 * @package App\Services\Server\Site\Repository\Providers
 */
class GitHub
{
    /**
     * @param User $user
     * @throws \Exception
     */
    public function getRepositories(User $user)
    {
        $this->setToken($user);

        return GitHub::api('repo')->all();
    }

    public function setSshKey()
    {
        dd('here');
    }

    /**
     * @param User $user
     * @return mixed
     * @throws \Exception
     */
    private function setToken(User $user)
    {
        if ($userRepositoryProvider = $user->userRepositoryProviders->where('service', 'github')->first()) {
            return config(['github.connections.main.token' => $userRepositoryProvider->token]);
        }

        throw new \Exception('No server provider found for this user');
    }
}