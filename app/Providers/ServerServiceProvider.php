<?php

namespace App\Providers;

use App\Contracts\Server\ProvisionServiceContract;
use App\Contracts\Server\ServerServiceContract;
use App\Services\Server\ProvisionService;
use App\Services\Server\ServerService;
use Illuminate\Support\ServiceProvider;

class ServerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

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
            ProvisionServiceContract::class,
            ProvisionService::class
        );
    }
}
