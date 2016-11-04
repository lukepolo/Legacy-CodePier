<?php

namespace App\Providers;

use App\Models\Server\Server;
use App\Models\Server\ServerCronJob;
use App\Models\Server\ServerFirewallRule;
use App\Models\Server\ServerSshKey;
use App\Models\Server\ServerWorker;
use App\Models\Site\Site;
use App\Models\User\User;
use App\Observers\Server\ServerCronJobObserver;
use App\Observers\Server\ServerFirewallRuleObserver;
use App\Observers\Server\ServerObserver;
use App\Observers\Server\ServerSshKeyObserver;
use App\Observers\Server\ServerWorkerObserver;
use App\Observers\SiteObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

/**
 * Class AppServiceProvider.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        User::observe(UserObserver::class);
        Site::observe(SiteObserver::class);

        ServerSshKey::observe(ServerSshKeyObserver::class);
        ServerWorker::observe(ServerWorkerObserver::class);
        ServerCronJob::observe(ServerCronJobObserver::class);
        ServerFirewallRule::observe(ServerFirewallRuleObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
