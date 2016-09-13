<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProvider.
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            'App\SocialProviders\Slack\SlackExtendSocialite@handle',
            'SocialiteProviders\GitLab\GitLabExtendSocialite@handle',
            'SocialiteProviders\DigitalOcean\DigitalOceanExtendSocialite@handle',

        ],
        \App\Events\ServerProvisioned::class => [
            \App\Listeners\EmailSudoAndDatabasePasswords::class,
        ],
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
