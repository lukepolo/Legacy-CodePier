<?php

namespace App\Providers;

use App\Services\Server\ServerService;
use App\Services\Systems\SystemService;
use Illuminate\Support\ServiceProvider;
use App\Services\Server\ServerFeatureService;
use App\Contracts\Server\ServerServiceContract;
use App\Contracts\Systems\SystemServiceContract;
use App\Contracts\Server\ServerFeatureServiceContract;

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

        $this->app->bind(
            ServerFeatureServiceContract::class,
            ServerFeatureService::class
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            ServerServiceContract::class,
            SystemServiceContract::class,
            ServerFeatureServiceContract::class,
        ];
    }
}
