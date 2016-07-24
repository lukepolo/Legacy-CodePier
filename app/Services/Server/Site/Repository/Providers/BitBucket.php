<?php

namespace App\Services\Server\Site\Repository\Providers;

use App\Models\UserRepositoryProvider;
use Bitbucket\API\Http\Listener\OAuthListener;
use Bitbucket\API\User;
use Bitbucket\API\Users;
use GitHub as GitHubService;
use Github\Exception\ValidationFailedException;

/**
 * Class BitBucket
 * @package App\Services\Server\Site\Repository\Providers
 */
class BitBucket implements RepositoryContract
{
    private $oauthParams;

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

        $user = new User();

        $user->getClient()->addListener(
            new OAuthListener($this->oauthParams)
        );

        $slug = $this->getRepositorySlug($repository);

        $repositories = collect(json_decode($user->repositories()->get()->getContent(), true));

        $repositoryInfo = $repositories->first(function($key, $repository) use($slug) {
            return $repository['slug'] == $slug;
        });

        if ($repositoryInfo['is_private']) {
//            try {


            $users = new Users();

            $users->getClient()->addListener(
                new OAuthListener($this->oauthParams)
            );


            $users->sshKeys()->create('CodePier', $sshKey, 'https://codepier.io');
//            } catch (ValidationFailedException $e) {
//                if (!$e->getMessage() == 'Validation Failed: key is already in use') {
//                    throw new \Exception($e->getMessage());
//                }
//            }
        }
    }

    /**
     * Sets the token so we can connect to the users account
     * @param UserRepositoryProvider $userRepositoryProvider
     * @return mixed
     * @throws \Exception
     */
    public function setToken(UserRepositoryProvider $userRepositoryProvider)
    {
        $this->oauthParams = [
            'oauth_token' => $userRepositoryProvider->token,
            'oauth_token_secret' => $userRepositoryProvider->tokenSecret,
            'oauth_consumer_key' => env('OAUTH_BITBUCKET_CLIENT_ID'),
            'oauth_consumer_secret' => env('OAUTH_BITBUCKET_SECRET_ID'),
            'oauth_callback' => env('OAUTH_BITBUCKET_CALLBACK'),
        ];
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
}