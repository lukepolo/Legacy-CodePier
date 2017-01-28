<?php

namespace App\Services\Repository\Providers;

use App\Models\Site\Site;
use Gitlab\Api\Repositories;
use Gitlab\Exception\RuntimeException;
use App\Models\User\UserRepositoryProvider;
use Guzzle\Http\Exception\ClientErrorResponseException;

class GitLab implements RepositoryContract
{
    use RepositoryTrait;

    private $client;

    /**
     * Imports a deploy key so we can clone the repositories.
     *
     * @param Site $site
     */
    public function importSshKey(Site $site)
    {
        $this->setToken($site->userRepositoryProvider);

        $client = new \Guzzle\Http\Client();

        try {
            $client->post('https://gitlab.com/api/v3/user/keys', [
                'Authorization' => 'Bearer '.$site->userRepositoryProvider->token,
                'Content-Type' => 'application/json',
            ], [
                'title' => $this->sshKeyLabel($site),
                'key' => $site->public_ssh_key,
            ])->send();
        } catch (ClientErrorResponseException $e) {
            // They have terrible error codes
            if (str_contains($e->getMessage(), '400')) {
                $this->throwKeyAlreadyUsed();
            }
            throw $e;
        }
    }

    /**
     * @param UserRepositoryProvider $userRepositoryProvider
     */
    public function setToken(UserRepositoryProvider $userRepositoryProvider)
    {
        $this->client = new \Gitlab\Client($userRepositoryProvider->repositoryProvider->url.'/api/v3/'); // change here
        $this->client->authenticate($userRepositoryProvider->token, \Gitlab\Client::AUTH_OAUTH_TOKEN);
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

        $lastCommit = collect($this->client->api('repositories')->commits($repository, 0, Repositories::PER_PAGE, $branch))->first();

        if (! empty($lastCommit)) {
            return [
                'git_commit' => $lastCommit['short_id'],
                'commit_message' => $lastCommit['message'],
            ];
        }
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
            $repositoryInfo = $this->client->api('projects')->show($site->repository);
        } catch (RuntimeException $e) {
            if ($e->getCode() == 404) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Site $site
     * @return Site
     */
    public function createDeployHook(Site $site)
    {
        $this->setToken($site->userRepositoryProvider);

        $webhook = $this->client->api('projects')->addHook($site->repository, [
            'push_events'           => true,
            'url'                   => action('WebHookController@deploy', $site->encode()),
        ]);

        $site->automatic_deployment_id = $webhook['id'];
        $site->save();

        return $site;
    }

    /**
     * @param Site $site
     * @return Site
     */
    public function deleteDeployHook(Site $site)
    {
        $this->setToken($site->userRepositoryProvider);

        $this->client->api('projects')->removeHook($site->repository, $site->automatic_deployment_id);

        $site->automatic_deployment_id = null;
        $site->save();

        return $site;
    }
}
