<?php

namespace App\Services\Repository\Providers;

use GuzzleHttp\Client;
use App\Models\Site\Site;
use App\Exceptions\DeployHookFailed;
use GuzzleHttp\Exception\ClientException;
use App\Models\User\UserRepositoryProvider;

class GitLab implements RepositoryContract
{
    use RepositoryTrait;

    /** @var Client */
    private $client;
    private $gitlab_url = 'https://gitlab.com/api/v4';

    /**
     * @param UserRepositoryProvider $userRepositoryProvider
     */
    public function setToken(UserRepositoryProvider $userRepositoryProvider)
    {
        $this->client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$userRepositoryProvider->token,
            ],
        ]);
    }

    /**
     * @param UserRepositoryProvider $userRepositoryProvider
     * @return mixed|string
     */
    public function getToken(UserRepositoryProvider $userRepositoryProvider)
    {
        return $userRepositoryProvider->token;
    }

    /**
     * @param Site $site
     * @return mixed
     */
    public function getRepository(Site $site)
    {
        $owner = $this->getRepositoryUser($site->repository);
        $slug = $this->getRepositorySlug($site->repository);

        return json_decode($this->client->get($this->gitlab_url.'/projects/'.urlencode($owner.'/'.$slug))->getBody());
    }

    /**
     * @param Site $site
     * @return Site
     * @throws DeployHookFailed
     */
    public function createDeployHook(Site $site)
    {
        $this->setToken($site->userRepositoryProvider);

        try {
            $webhook = json_decode($this->client->post($this->gitlab_url.'/projects/'.$this->getRepository($site)->id.'/hooks', [
                'form_params' => [
                    'push_events' => true,
                    'url' => action('WebHookController@deploy', $site->hash),
                ],
            ])->getBody());

            $site->automatic_deployment_id = $webhook->id;
            $site->save();
        } catch (ClientException $e) {
            if ($e->getCode()) {
                throw new DeployHookFailed('We could not create the webhook, please make sure you have access to the repository');
            }
            throw new DeployHookFailed($e->getMessage());
        }

        return $site;
    }

    /**
     * @param Site $site
     * @return Site
     */
    public function deleteDeployHook(Site $site)
    {
        $this->setToken($site->userRepositoryProvider);

        $this->client->delete($this->gitlab_url.'/projects/'.$this->getRepository($site)->id.'/hooks/'.$site->automatic_deployment_id);

        $site->automatic_deployment_id = null;
        $site->save();

        return $site;
    }
}
