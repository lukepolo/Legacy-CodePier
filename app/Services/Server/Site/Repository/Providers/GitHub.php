<?php

namespace App\Services\Server\Site\Repository\Providers;

use App\Models\Server;
use App\Models\User;
use GitHub as GitHubService;

class GitHub
{
    public function getRepositories(User $user)
    {
        $this->setToken($user);

        return GitHubService::api('repo')->all();
    }

    public function importSshKey(Server $server, User $user)
    {
        $this->setToken($user);

        $key = GitHubService::api('repo')->keys()->create('lukepolo', 'codepier', [
            'title' => 'key title',
            'key' => env('SSH_KEY'),
        ]);

        dd($key);
        dd($this->getSshKeys());
    }

    private function getSshKeys()
    {
        return GitHubService::api('repo')->keys()->all('lukepolo', 'codepier');
    }

    private function setToken(User $user)
    {
        if ($userRepositoryProvider = $user->userRepositoryProviders->where('service', 'github')->first()) {
            return config(['github.connections.main.token' => $userRepositoryProvider->token]);
        }

        throw new \Exception('No server provider found for this user');
    }
}