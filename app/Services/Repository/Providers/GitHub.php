<?php

namespace App\Services\Repository\Providers;

use App\Exceptions\DeployHookFailed;
use App\Models\Site\Site;
use App\Models\User\UserRepositoryProvider;
use Github\Api\CurrentUser;
use Github\Api\Repo;
use Github\Client;
use Github\Exception\RuntimeException;

class GitHub implements RepositoryContract
{
    use RepositoryTrait;

    /** @var CurrentUser */
    private $meApi;
    private $client;
    /** @var Repo */
    private $repositoryApi;

    /**
     * GitHub constructor.
     */
    public function __construct()
    {
        /* @var Client $client */
        $this->client = new Client();
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
        $this->client->authenticate($userRepositoryProvider->token, 'http_token');
        $this->meApi = $this->client->currentUser();
        $this->repositoryApi = $this->client->repository();
    }

    /**
     * @param Site $site
     *
     * @throws DeployHookFailed
     *
     * @return Site
     */
    public function createDeployHook(Site $site)
    {
        $this->setToken($site->userRepositoryProvider);

        $owner = $this->getRepositoryUser($site->repository);
        $slug = $this->getRepositorySlug($site->repository);

        try {
            $webhook = $this->repositoryApi->hooks()->create($owner, $slug, [
                'name'   => 'web',
                'active' => true,
                'events' => [
                    'push',
                ],
                'config' => [
                    'url'          => action('WebHookController@deploy', $site->hash),
                    'content_type' => 'json',
                ],
            ]);
        } catch (RuntimeException $e) {
            if ('Not Found' == $e->getMessage()) {
                throw new DeployHookFailed('We could not create the webhook, please make sure you have access to the repository');
            }

            throw new DeployHookFailed($e->getMessage());
        }

        $site->automatic_deployment_id = $webhook['id'];
        $site->save();

        return $site;
    }

    /**
     * @param UserRepositoryProvider $userRepositoryProvider
     *
     * @return mixed|string
     */
    public function getToken(UserRepositoryProvider $userRepositoryProvider)
    {
        return $userRepositoryProvider->token;
    }

    /**
     * @param Site $site
     *
     * @return Site
     */
    public function deleteDeployHook(Site $site)
    {
        $this->setToken($site->userRepositoryProvider);

        $this->repositoryApi->hooks()->remove(
            $this->getRepositoryUser($site->repository),
            $this->getRepositorySlug($site->repository),
            $site->automatic_deployment_id
        );

        $site->automatic_deployment_id = null;
        $site->save();

        return $site;
    }
}
