<?php

namespace App\Services\Repository\Providers;

use GuzzleHttp\Client;
use App\Models\Site\Site;
use Gitlab\Exception\RuntimeException;
use GuzzleHttp\Exception\ClientException;
use App\Models\User\UserRepositoryProvider;

class GitLab implements RepositoryContract
{
    use RepositoryTrait;

    private $client;

    /**
     * Imports a deploy key so we can clone the repositories.
     *
     * @param Site $site
     * @throws \Exception
     */
    public function importSshKey(Site $site)
    {
        $this->setToken($site->userRepositoryProvider);

        $client = new Client();

        try {
            $client->post('https://gitlab.com/api/v3/user/keys', [
                'Authorization' => 'Bearer '.$site->userRepositoryProvider->token,
                'Content-Type' => 'application/json',
            ], [
                'title' => $this->sshKeyLabel($site),
                'key' => $site->public_ssh_key,
            ])->send();
        } catch (ClientException $e) {
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
            $this->client->api('projects')->show($site->repository);
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
            'url'                   => action('WebHookController@deploy', $site->hash),
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
