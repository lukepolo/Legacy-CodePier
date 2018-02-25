<?php

namespace App\Providers;

use App\Services\Buoys\BuoyService;
use App\Contracts\BuoyServiceContract;
use Illuminate\Support\ServiceProvider;

class BuoyServiceProvider extends ServiceProvider
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
            BuoyServiceContract::class,
            BuoyService::class
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
            BuoyServiceContract::class,
        ];
    }
}
