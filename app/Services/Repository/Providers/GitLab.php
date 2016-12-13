<?php

namespace App\Services\Repository\Providers;

use Gitlab\Client;
use Gitlab\Api\Projects;
use App\Models\Site\Site;
use Gitlab\Api\Repositories;
use App\Models\User\UserRepositoryProvider;

class GitLab implements RepositoryContract
{
    use RepositoryTrait;

    /** @var Client $client */
    private $client;

    /**
     * Imports a deploy key so we can clone the repositories.
     *
     * @param \App\Models\User\UserRepositoryProvider $userRepositoryProvider
     * @param Site $site
     * @param $sshKey
     */
    public function importSshKeyIfPrivate(UserRepositoryProvider $userRepositoryProvider, Site $site, $sshKey)
    {
        $repository = $site->repository;

        $this->setToken($userRepositoryProvider);

        if ($this->isRepositoryPrivate($repository)) {
            $this->isPrivate($site, true);
            $repositoryInfo = $this->getRepositoryInfo($repository);

            $client = new \Guzzle\Http\Client();

            $client->post('https://gitlab.com/api/v3/projects/'.$repositoryInfo['id'].'/deploy_keys', [
                'Authorization' => 'Bearer 091867cdd5d0b0f3a6b12615f98e58faae08e1da9e8e19db0ff2688148544219',
                'Content-Type' => 'application/json',
            ], [
                'title' => 'CodePier',
                'key' => $sshKey,
            ])->send();

            return;
        }

        $this->isPrivate($site, false);
    }

    public function isRepositoryPrivate($repository)
    {
        $repositoryInfo = $this->getRepositoryInfo($repository);

        if (isset($repositoryInfo['public'])) {
            return ! $repositoryInfo['public'];
        }

        return true;
    }

    /**
     * Gets the repository information.
     *
     * @param $repository
     *
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
     * Gets the users repositories username // TODO - move to a trait.
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
     * Gets the users repositories name // TODO - move to a trait.
     *
     * @param $repository
     *
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

        if (! empty($lastCommit)) {
            dd($lastCommit);
            return [
                'git_commit' => $lastCommit['short_id'],
                'commit_message' => $lastCommit['message']
            ];
        }
    }

    public function createDeployHook(Site $site)
    {
        $this->setToken($site->userRepositoryProvider);

        $webhook = $this->client->api('projects')->addHook($site->repository, [
            'push_events'           => true,
            'merge_requests_events' => true,
            'url'                   => action('WebHookController@deploy', $site->encode()),
        ]);

        $site->automatic_deployment_id = $webhook['id'];
        $site->save();

        return $site;
    }

    public function deleteDeployHook(Site $site)
    {
        $this->setToken($site->userRepositoryProvider);

        $this->client->api('projects')->removeHook($site->repository, $site->automatic_deployment_id);

        $site->automatic_deployment_id = null;
        $site->save();

        return $site;
    }
}
