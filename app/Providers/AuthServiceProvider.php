<?php

namespace App\Providers;

use App\Guards\JwtGuard;
use Illuminate\Auth\RequestGuard;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\TokenRepository;
use League\OAuth2\Server\ResourceServer;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

        Auth::extend('jwt', function ($app, $name, array $config) {
            return new RequestGuard(function ($request) use ($config) {
                return (new JwtGuard(
                    $this->app->make(ResourceServer::class),
                    Auth::createUserProvider($config['provider']),
                    $this->app->make(TokenRepository::class),
                    $this->app->make(ClientRepository::class),
                    $this->app->make('encrypter')
                ))->user($request);
            }, $this->app['request']);
        });
    }
}
