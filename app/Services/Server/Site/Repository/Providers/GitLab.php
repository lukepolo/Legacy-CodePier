<?php

namespace App\Services\Server\Site\Repository\Providers;

use App\Models\Site;
use App\Models\UserRepositoryProvider;
use GitHub as GitHubService;
use Github\Exception\ValidationFailedException;
use Vinkla\GitLab\Facades\GitLab;

/**
 * Class GitHub
 * @package App\Services\Server\Site\Repository\Providers
 */
class GitLab implements RepositoryContract
{
    /**
     * Imports a deploy key so we can clone the repositories
     *
     * @param UserRepositoryProvider $userRepositoryProvider
     * @param $repository
     * @param $sshKey
     * @throws \Exception
     */
    public function importSshKeyIfPrivate(UserRepositoryProvider $userRepositoryProvider, $repository, $sshKey)
    {
        $this->setToken($userRepositoryProvider);

        if($this->isRepositoryPrivate($repository)) {
            try {
                GitHubService::api('repo')->keys()->create(
                    $this->getRepositoryUser($repository),
                    $this->getRepositorySlug($repository),
                    [
                        'title' => 'CodePier',
                        'key' => $sshKey,
                    ]
                );
            } catch (ValidationFailedException $e) {
                if (!$e->getMessage() == 'Validation Failed: key is already in use') {
                    throw new \Exception($e->getMessage());
                }
            }
        }
    }

    public function isRepositoryPrivate($repository)
    {
        $repositoryInfo = $this->getRepositoryInfo($repository);

        dd($repositoryInfo);
        if(isset($repositoryInfo['private'])) {
            return $repositoryInfo['private'];
        }
        return false;
    }

    /**
     * Gets the repository information
     *
     * @param $repository
     * @return mixed
     */
    public function getRepositoryInfo($repository)
    {
        return GitLab::api('repo')->show(
            $this->getRepositoryUser($repository),
            $this->getRepositorySlug($repository)
        );
    }

    public function setToken(UserRepositoryProvider $userRepositoryProvider)
    {
        config(['gitlab.connections.main.token' => $userRepositoryProvider->token]);
        config(['gitlab.connections.main.base_url' => 'http://'.$userRepositoryProvider->repositoryProvider->url.'/v3/']);
    }

    /**
     * Gets the users repositories username // TODO - move to a trait
     * @param $repository
     * @return mixed
     */
    public function getRepositoryUser($repository)
    {
        return explode('/', $repository)[0];
    }

    /**
     * Gets the users repositories name // TODO - move to a trait
     * @param $repository
     * @return mixed
     */
    public function getRepositorySlug($repository)
    {
        return explode('/', $repository)[1];
    }

    public function getLatestCommit(UserRepositoryProvider $userRepositoryProvider, $repository, $branch)
    {
        $this->setToken($userRepositoryProvider);

        $lastCommit = collect(GitHubService::api('repo')->commits()->all($this->getRepositoryUser($repository), $this->getRepositorySlug($repository), ['sha' => $branch]))->first();

        if(!empty($lastCommit)) {
            return $lastCommit['sha'];
        }

        return null;
    }

    public function createDeployHook(Site $site)
    {
        $this->setToken($site->userRepositoryProvider);

        $webhook = GitHubService::api('repo')->hooks()->create($this->getRepositoryUser($site->repository), $this->getRepositorySlug($site->repository), [
            'name'   => 'web',
            'active' => true,
            'events' => [
                'push',
                'pull_request'
            ],
            'config' => [
                'url'          => route('webhook/deploy', $site->encode()),
                'content_type' => 'json'
            ]
        ]);

        $site->automatic_deployment_id = $webhook['id'];
        $site->save();
    }

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

    }
}