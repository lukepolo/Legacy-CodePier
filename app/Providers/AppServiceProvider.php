<?php

namespace App\Providers;

use Carbon\Carbon;
use App\Models\Site\Site;
use App\Models\User\User;
use Illuminate\Support\Facades\Blade;
use Laravel\Horizon\Horizon;
use App\Models\Server\Server;
use App\Models\ServerCommand;
use App\Models\Site\Lifeline;
use App\Models\SslCertificate;
use Laravel\Passport\Passport;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\URL;
use App\Observers\Site\SiteObserver;
use App\Models\User\UserLoginProvider;
use App\Models\User\UserServerProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use App\Observers\Server\ServerObserver;
use App\Observers\Site\LifelineObserver;
use App\Models\Site\SiteServerDeployment;
use App\Observers\SslCertificateObserver;
use App\Models\User\UserNotificationProvider;
use App\Observers\Server\ServerCommandObserver;
use App\Observers\Server\ServerDeploymentObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::doubleEncode();

        Passport::tokensCan([
            'create-custom-server' => 'Allows creation of a custom server',
        ]);

        if ($this->app->environment() != 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        User::observe(UserObserver::class);
        Site::observe(SiteObserver::class);
        Server::observe(ServerObserver::class);
        Lifeline::observe(LifelineObserver::class);
        SslCertificate::observe(SslCertificateObserver::class);
        ServerCommand::observe(ServerCommandObserver::class);
        SiteServerDeployment::observe(ServerDeploymentObserver::class);

        // TODO - what are these suppose to be doing?
        UserLoginProvider::updating(function ($provider) {
            if (! empty($expiresIn = $provider->getOriginal('expires_at'))) {
                $provider->expires_at = Carbon::now()->addSeconds($expiresIn);
            }
        });

        UserNotificationProvider::updating(function ($provider) {
            if (! empty($expiresIn = $provider->getOriginal('expires_at'))) {
                $provider->expires_at = Carbon::now()->addSeconds($expiresIn);
            }
        });

        UserServerProvider::updating(function (Model $provider) {
            if (! empty($expiresIn = $provider->getOriginal('expires_at'))) {
                $provider->expires_at = Carbon::now()->addSeconds($expiresIn);
            }
        });

        if (config('app.force_https')) {
            URL::forceScheme('https');
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Horizon::auth(function ($request) {
            if ($request->user()) {
                if (config('app.env') === 'local') {
                    return true;
                }

                return strtolower($request->user()->role) === 'admin';
            }
        });

        Horizon::routeSlackNotificationsTo(config('services.slack.horizon'));
    }
}
