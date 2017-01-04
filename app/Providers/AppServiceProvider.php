<?php

namespace App\Providers;

use App\Models\File;
use App\Models\Site\Site;
use App\Models\User\User;
use App\Models\ServerCommand;
use App\Observers\UserObserver;
use App\Observers\Site\SiteObserver;
use Illuminate\Support\ServiceProvider;
use App\Models\Server\ServerNetworkRule;
use App\Observers\Site\SiteFileObserver;
use App\Models\Site\SiteServerDeployment;
use Illuminate\Support\Facades\Validator;
use App\Observers\Server\ServerCommandObserver;
use App\Observers\Server\ServerDeploymentObserver;
use App\Observers\Server\ServerNetworkRuleObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->environment() != 'production') {
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        User::observe(UserObserver::class);

        Site::observe(SiteObserver::class);
        File::observe(SiteFileObserver::class);
        ServerCommand::observe(ServerCommandObserver::class);
        ServerNetworkRule::observe(ServerNetworkRuleObserver::class);
        SiteServerDeployment::observe(ServerDeploymentObserver::class);

        Validator::extend('domain', function ($attribute, $value) {
            if (! is_string($value) && ! is_numeric($value)) {
                return false;
            }

            return preg_match('/^[\pL\pM\pN\.]+$/u', $value) > 0;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
