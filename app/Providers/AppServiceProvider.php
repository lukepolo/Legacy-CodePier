<?php

namespace App\Providers;

use App\Models\Server\Server;
use App\Models\ServerCommand;
use App\Models\Site\Lifeline;
use App\Models\Site\Site;
use App\Models\Site\SiteServerDeployment;
use App\Models\SslCertificate;
use App\Models\User\User;
use App\Models\User\UserLoginProvider;
use App\Models\User\UserNotificationProvider;
use App\Models\User\UserServerProvider;
use App\Observers\Server\ServerCommandObserver;
use App\Observers\Server\ServerDeploymentObserver;
use App\Observers\Server\ServerObserver;
use App\Observers\Site\LifelineObserver;
use App\Observers\Site\SiteObserver;
use App\Observers\SslCertificateObserver;
use App\Observers\UserObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Horizon\Horizon;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ('production' !== $this->app->environment()) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        Passport::tokensCan([
            'create-custom-server' => 'Allows creation of a custom server',
        ]);

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
                if ('local' === config('app.env')) {
                    return true;
                }

                return 'admin' === strtolower($request->user()->role);
            }
        });

        Horizon::routeSlackNotificationsTo(config('services.slack.horizon'));
    }
}
