<?php

namespace App\SocialProviders\Slack;

use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use SocialiteProviders\Manager\OAuth2\User;

/**
 * Class Provider
 * @package App\SoicalProviders\Slack
 */
class Provider extends \SocialiteProviders\Slack\Provider
{
    protected $scopes = ['channels:read chat:write:bot'];
}
