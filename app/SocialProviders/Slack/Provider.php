<?php

namespace App\SocialProviders\Slack;

/**
 * Class Provider.
 */
class Provider extends \SocialiteProviders\Slack\Provider
{
    protected $scopes = ['channels:read chat:write:bot'];
}
