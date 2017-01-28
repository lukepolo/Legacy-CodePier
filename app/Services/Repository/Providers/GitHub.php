<?php

namespace App\Services\Repository\Providers;

use App\Models\Site\Site;
use GitHub as GitHubService;
use Github\Exception\RuntimeException;
use App\Models\User\UserRepositoryProvider;
use Github\Exception\ValidationFailedException;

class GitHub implements RepositoryContract
{
    use RepositoryTrait;

    /**
     * Imports a deploy key so we can clone the repositories.
     * @param Site $site
     * @return string|void
     * @throws \Exception
     */
    public function importSshKey(Site $site)
    {
        $this->setToken($site->userRepositoryProvider);

        try {
            GitHubService::me()->keys()->create([
                'title' => $this->sshKeyLabel($site),
                'key'   => $site->public_ssh_key,
            ]);
        } catch (ValidationFailedException $e) {
            if ($e->getMessage() == 'Validation Failed: key is already in use') {
                $this->throwKeyAlreadyUsed();
            }

            throw $e;
        }
    }

    /**
     * Sets the token so we can connect to the users account.
     *
     * @param UserRepositoryProvider $userRepositoryProvider
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function setToken(UserRepositoryProvider $userRepositoryProvider)
    {
        return config(['github.connections.main.token' => $userRepositoryProvider->token]);
    }

    /**
     * @param UserRepositoryProvider $userRepositoryProvider
     * @param $repository
     * @param $branch
     * @return array
     */
    public function getLatestCommit(UserRepositoryProvider $userRepositoryProvider, $repository, $branch)
    {
        $this->setToken($userRepositoryProvider);

        $lastCommit = null;

        try {
            $lastCommit = collect(GitHubService::api('repo')->commits()->all($this->getRepositoryUser($repository), $this->getRepositorySlug($repository), ['sha' => $branch]))->first();
        } catch (RuntimeException $e) {
        }

        if (! empty($lastCommit)) {
            return [
                'git_commit' => $lastCommit['sha'],
                'commit_message' => $lastCommit['commit']['message'],
            ];
        }
    }

    /**
     * @param Site $site
     * @return Site
     */
    public function createDeployHook(Site $site)
    {
        $this->setToken($site->userRepositoryProvider);

        $webhook = GitHubService::api('repo')->hooks()->create($this->getRepositoryUser($site->repository), $this->getRepositorySlug($site->repository), [
            'name'   => 'web',
            'active' => true,
            'events' => [
                'push',
            ],
            'config' => [
                'url'          => action('WebHookController@deploy', $site->encode()),
                'content_type' => 'json',
            ],
        ]);

        $site->automatic_deployment_id = $webhook['id'];
        $site->save();

        return $site;
    }

    /**
     * Checks if the repository is private.
     *
     * @param Site $site
     *
     * @return bool
     */
    public function isPrivate(Site $site)
    {
        $this->setToken($site->userRepositoryProvider);

        try {
            $repository = GitHubService::api('repo')->show(
                $this->getRepositoryUser($site->repository),
                $this->getRepositorySlug($site->repository)
            );
        } catch (RuntimeException $e) {
            if ($e->getMessage() == 'Not Found') {
                return true;
            }
        }

        if(!empty($repository) && $repository['private']) {
            return $repository['private'];
        }

        return false;
    }

    /**
     * @param Site $site
     * @return Site
     */
    public function deleteDeployHook(Site $site)
    {
        $this->setToken($site->userRepositoryProvider);

        GitHubService::api('repo')->hooks()->remove(
            $this->getRepositoryUser($site->repository),
            $this->getRepositorySlug($site->repository),
            $site->automatic_deployment_id
        );

        $site->automatic_deployment_id = null;
        $site->save();

        return $site;
    }
}
