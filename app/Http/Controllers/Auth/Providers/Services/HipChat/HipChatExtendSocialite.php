<?php

namespace App\Http\Controllers\Auth\Providers\Services\HipChat;

use SocialiteProviders\Manager\SocialiteWasCalled;

class HipChatExtendSocialite
{
    /**
     * Execute the provider.
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('hipchat', __NAMESPACE__.'\Provider');
    }
}