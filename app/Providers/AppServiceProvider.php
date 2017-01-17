<?php

namespace App\Providers;

use App\Models\Site\Site;
use App\Models\User\User;
use App\Models\ServerCommand;
use App\Models\User\UserLoginProvider;
use App\Models\User\UserNotificationProvider;
use App\Models\User\UserServerProvider;
use App\Observers\UserObserver;
use App\Observers\Site\SiteObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use App\Models\Server\ServerNetworkRule;
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
        ServerCommand::observe(ServerCommandObserver::class);
        ServerNetworkRule::observe(ServerNetworkRuleObserver::class);
        SiteServerDeployment::observe(ServerDeploymentObserver::class);

        Validator::extend('server_name', function ($attribute, $value) {
            return preg_match('/^[a-zA-Z0-9\.\-]+$/', $value) > 0;
        });

        Validator::extend('domain', function ($attribute, $value) {
            if (! is_string($value) && ! is_numeric($value)) {
                return false;
            }

            return preg_match('/^[\pL\pM\pN\.]+$/u', $value) > 0;
        });

        UserLoginProvider::updating(function($provider) {
            if(!empty($expiresIn = $provider->getOriginal('expires_in'))) {
                $provider->expires_in = Carbon::now()->addSeconds($expiresIn);
            }
        });

        UserNotificationProvider::updating(function($provider) {
            if(!empty($expiresIn = $provider->getOriginal('expires_in'))) {
                $provider->expires_in = Carbon::now()->addSeconds($expiresIn);
            }
        });

        UserServerProvider::updating(function(Model $provider) {
            if(!empty($expiresIn = $provider->getOriginal('expires_in'))) {
                $provider->expires_in = Carbon::now()->addSeconds($expiresIn);
            }
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
