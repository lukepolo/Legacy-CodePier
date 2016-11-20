<?php

namespace App\Providers;

use App\Contracts\Server\ServerServiceContract;
use App\Contracts\Systems\SystemServiceContract;
use App\Services\Server\ServerService;
use App\Services\Systems\SystemService;
use Illuminate\Support\ServiceProvider;

class ServerServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            ServerServiceContract::class,
            ServerService::class
        );

        $this->app->bind(
            SystemServiceContract::class,
            SystemService::class
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [SystemServiceContract::class];
    }

}
