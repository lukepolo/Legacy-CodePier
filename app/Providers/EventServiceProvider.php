<?php

namespace App\Providers;

use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\GitLab\GitLabExtendSocialite;
use SocialiteProviders\DigitalOcean\DigitalOceanExtendSocialite;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        SocialiteWasCalled::class => [
            \App\SocialProviders\Slack\SlackExtendSocialite::class.'@handle',
            GitLabExtendSocialite::class.'@handle',
            DigitalOceanExtendSocialite::class.'@handle',
        ],
        \Illuminate\Auth\Events\Login::class => [
            \App\Listeners\LoginSuccessful::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
