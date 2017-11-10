<?php

namespace App\Services\Repository\Providers;

use Github\Client;
use Github\Api\Repo;
use App\Models\Site\Site;
use Github\Api\CurrentUser;
use App\Exceptions\DeployHookFailed;
use Github\Exception\RuntimeException;
use App\Models\User\UserRepositoryProvider;
use Github\Exception\ValidationFailedException;

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
     * Imports a deploy key so we can clone the repositories.
     * @param Site $site
     * @return string|void
     * @throws \Exception
     */
    public function importSshKey(Site $site)
    {
        $this->setToken($site->userRepositoryProvider);

        try {
            $this->meApi->keys()->create([
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
        $this->client->authenticate($userRepositoryProvider->token, 'http_token');
        $this->meApi = $this->client->currentUser();
        $this->repositoryApi = $this->client->repository();
    }

    /**
     * @param Site $site
     * @return Site
     * @throws DeployHookFailed
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
            if ($e->getMessage() == 'Not Found') {
                if ($site->private) {
                    throw new DeployHookFailed('We could not create the webhook, please make sure you have access to the repository');
                }
                throw new DeployHookFailed('We could not create the webhook as it is not owned by you. Please fork the repository ('.$owner.'/'.$slug.') to allow for this feature.');
            }

            throw new DeployHookFailed($e->getMessage());
        }

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
            $repository = $this->repositoryApi->show(
                $this->getRepositoryUser($site->repository),
                $this->getRepositorySlug($site->repository)
            );
        } catch (RuntimeException $e) {
            if ($e->getMessage() == 'Not Found') {
                return true;
            }
        }

        if (! empty($repository) && $repository['private']) {
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
