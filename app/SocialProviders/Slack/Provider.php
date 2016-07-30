<?php

namespace App\SoicalProviders\Slack;

use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends \SocialiteProviders\Slack\Provider
{
    protected $scopes = [];
}
