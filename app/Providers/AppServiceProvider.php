<?php

namespace App\Providers;

use Carbon\Carbon;
use App\Models\Site\Site;
use App\Models\User\User;
use App\Models\Server\Server;
use App\Models\ServerCommand;
use App\Models\Site\Lifeline;
use App\Models\SslCertificate;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Passport;
use App\Observers\UserObserver;
use App\Observers\Site\SiteObserver;
use App\Models\User\UserLoginProvider;
use App\Models\User\UserServerProvider;
use App\Services\Systems\SystemService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use App\Observers\Server\ServerObserver;
use App\Observers\Site\LifelineObserver;
use App\Models\Site\SiteServerDeployment;
use App\Observers\SslCertificateObserver;
use Illuminate\Support\Facades\Validator;
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

        DB::beginTransaction();

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

        Validator::extend('server_name', function ($attribute, $value) {
            return preg_match('/^[a-zA-Z0-9\.\-]+$/', $value) > 0;
        });

        Validator::extend('domain', function ($attribute, $value) {
            return preg_match('/^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$/', $value) > 0;
        });

        Validator::extend('greaterThanZero', function ($attribute, $value) {
            return $value > 0;
        });

        // http://stackoverflow.com/questions/2821043/allowed-characters-in-linux-environment-variable-names
        // https://regex101.com/r/nBGmWp/1
        Validator::extend('environmentVariable', function ($attribute, $value) {
            return preg_match('/^([a-zA-Z_])([a-zA-Z0-9_])+$/', $value) > 0;
        });

        Validator::extend('databaseName', function ($attribute, $value) {
            return preg_match('/^([a-zA-Z_])([a-zA-Z0-9_])+$/', $value) > 0;
        });

        Validator::extend('valid_language_type', function ($attribute, $value) {
            return collect([
                'PHP',
                'Ruby',
            ])->contains($value);
        });

        Validator::extend('valid_server_type', function ($attribute, $value) {
            return collect(SystemService::SERVER_TYPES)->contains($value);
        });

        Validator::extend('valid_firewall_port', function ($attribute, $value) {
            return is_numeric($value) || $value == '*';
        });

        UserLoginProvider::updating(function ($provider) {
            if (! empty($expiresIn = $provider->getOriginal('expires_in'))) {
                $provider->expires_in = Carbon::now()->addSeconds($expiresIn);
            }
        });

        UserNotificationProvider::updating(function ($provider) {
            if (! empty($expiresIn = $provider->getOriginal('expires_in'))) {
                $provider->expires_in = Carbon::now()->addSeconds($expiresIn);
            }
        });

        UserServerProvider::updating(function (Model $provider) {
            if (! empty($expiresIn = $provider->getOriginal('expires_in'))) {
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
