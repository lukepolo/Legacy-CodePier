<?php

namespace App\Services\Server\Site\Repository\Providers;

use App\Models\Site;
use App\Models\UserRepositoryProvider;
use Gitlab\Api\Repositories;

/**
 * Class GitHub
 * @package App\Services\Server\Site\Repository\Providers
 */
class GitLab implements RepositoryContract
{
    private $client;

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
            $this->client->api('projects')->addKey($repository, 'CodePier', $sshKey);
        }
    }

    public function isRepositoryPrivate($repository)
    {
        $repositoryInfo = $this->getRepositoryInfo($repository);

        if(isset($repositoryInfo['public'])) {
            return !$repositoryInfo['public'];
        }
        return true;
    }

    /**
     * Gets the repository information
     *
     * @param $repository
     * @return mixed
     */
    public function getRepositoryInfo($repository)
    {
        return $this->client->api('projects')->show($repository);
    }

    public function setToken(UserRepositoryProvider $userRepositoryProvider)
    {
        $this->client = new \Gitlab\Client($userRepositoryProvider->repositoryProvider->url.'/api/v3/'); // change here
        $this->client->authenticate($userRepositoryProvider->token, \Gitlab\Client::AUTH_OAUTH_TOKEN);
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

        $lastCommit = collect($this->client->api('repositories')->commits($repository, 0, Repositories::PER_PAGE, $branch))->first();

        if(!empty($lastCommit)) {
            return $lastCommit['short_id'];
        }

        return null;
    }

    public function createDeployHook(Site $site)
    {
        $this->setToken($site->userRepositoryProvider);

        $webhook = $this->client->api('projects')->addHook($site->repository, [
            'push_events' => true,
            'merge_requests_events' => true,
            'url' => route('webhook/deploy', $site->encode()),
        ]);

        $site->automatic_deployment_id = $webhook['id'];
        $site->save();
    }

    public function deleteDeployHook(Site $site)
    {
        $this->setToken($site->userRepositoryProvider);

        $this->client->api('projects')->removeHook($site->repository, $site->automatic_deployment_id);

        $site->automatic_deployment_id = null;
        $site->save();
    }
}