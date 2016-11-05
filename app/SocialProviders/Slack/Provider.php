<?php

namespace App\SocialProviders\Slack;

class Provider extends \SocialiteProviders\Slack\Provider
{
    protected $scopes = ['channels:read chat:write:bot'];
}
