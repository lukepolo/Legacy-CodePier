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

    /** @var  Client */
    private $client;
    private $gitlab_url = 'https://gitlab.com/api/v4';

    /**
     * Imports a deploy key so we can clone the repositories.
     *
     * @param Site $site
     * @throws \Exception
     */
    public function importSshKey(Site $site)
    {
        $this->setToken($site->userRepositoryProvider);

        try {
            $this->client->post($this->gitlab_url.'/projects/'.$this->getRepository($site)->id.'/deploy_keys', [
                'form_params' => [
                    'title' => $this->sshKeyLabel($site),
                    'key' => $site->public_ssh_key,
                ]
            ]);
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
        $this->client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$userRepositoryProvider->token,
            ]
        ]);

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
            $repository = $this->getRepository($site);

            if(!empty($repository)) {
                return $repository->visibility === 'private';
            }

        } catch (ClientException $e) {
            if ($e->getCode() == 404) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Site $site
     * @return mixed
     */
    public function getRepository(Site $site) {

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

        $owner = $this->getRepositoryUser($site->repository);
        $slug = $this->getRepositorySlug($site->repository);

        try {
            $webhook = json_decode($this->client->post($this->gitlab_url.'/projects/'.$this->getRepository($site)->id.'/hooks', [
                'form_params' => [
                    'push_events' => true,
                    'url' => action('WebHookController@deploy', $site->hash),
                ]
            ])->getBody());

            $site->automatic_deployment_id = $webhook->id;
            $site->save();
        } catch (ClientException $e) {
            if ($e->getCode()) {
                if ($site->private) {
                    throw new DeployHookFailed('We could not create the webhook, please make sure you have access to the repository');
                }
                throw new DeployHookFailed('We could not create the webhook as it is not owned by you. Please fork the repository ('.$owner.'/'.$slug.') to allow for this feature.');
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

        $this->client->delete($this->gitlab_url . '/projects/' . $this->getRepository($site)->id . '/hooks/' . $site->automatic_deployment_id);

        $site->automatic_deployment_id = null;
        $site->save();

        return $site;
    }
}
