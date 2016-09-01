<?php

namespace App\SocialProviders\Slack;

use SocialiteProviders\Manager\SocialiteWasCalled;

/**
 * Class SlackExtendSocialite.
 */
class SlackExtendSocialite
{
    /**
     * Register the provider.
     *
     * @param \SocialiteProviders\Manager\SocialiteWasCalled $socialiteWasCalled
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite(
            'slack', __NAMESPACE__.'\Provider'
        );
    }
}
